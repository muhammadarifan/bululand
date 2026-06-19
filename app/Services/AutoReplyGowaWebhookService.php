<?php

namespace App\Services;

use App\DTO\GowaWebhookPayload;
use App\Models\Event;
use App\Models\EventContribution;
use App\Models\EventMoneyTransaction;
use App\Models\House;
use App\Services\Gowa\GowaMessageSender;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use function data_get;

class AutoReplyGowaWebhookService
{
    private const CACHE_PREFIX = 'gowa_menu_state_';

    public function __construct(private readonly GowaMessageSender $sender) {}

    public function handle(GowaWebhookPayload $payload): void
    {
        $reply = $this->replyForMessage($payload);

        try {
            Log::info('Auto reply', [
                'reply' => $reply,
                'payload' => $payload->all(),
            ]);
        } catch (\RuntimeException) {
            // Logging tidak tersedia (unit test)
        }

        if ($reply === null || $reply === '') {
            return;
        }

        $this->sendReply($payload, $reply);
    }

    protected function replyForMessage(GowaWebhookPayload $payload): ?string
    {
        $message = $payload->message();

        if (! is_string($message)) {
            return null;
        }

        $message = trim($message);
        $sender = $payload->sender();

        try {
            Log::info('Auto reply for message', [
                'message' => $message,
                'sender' => $sender,
            ]);
        } catch (\RuntimeException) {
            // Logging tidak tersedia (unit test)
        }

        // Handle /menu command (membutuhkan sender)
        if ($sender !== null && strtolower($message) === '/menu') {
            $this->clearUserState($sender);
            return $this->showMenu();
        }

        // Handle menu selection (membutuhkan sender)
        if ($sender !== null) {
            $menuReply = $this->handleMenuSelection($sender, $message);

            if ($menuReply !== null) {
                return $menuReply;
            }
        }

        // Fallback: sapa balik jika ada kata "halo"
        if (str_contains(strtolower($message), 'halo')) {
            return 'Halo juga!';
        }

        return null;
    }

    protected function showMenu(): string
    {
        return "╔═══ *MENU BOT GOWA* ═══╗\n\n"
            . "1️⃣  *Cek Iuran Event*\n"
            . "   Pilih event & cek status iuran rumah\n\n"
            . "2️⃣  *Laporan Keuangan*\n"
            . "   Lihat laporan pemasukan & pengeluaran\n\n"
            . "3️⃣  *Kembali ke Menu Awal*\n\n"
            . "╚════════════════════╝\n\n"
            . "Balas dengan angka *1*, *2*, atau *3*";
    }

    protected function handleMenuSelection(string $sender, string $body): ?string
    {
        // Check if user has an active state (waiting for input in a multi-step flow)
        $state = $this->getUserState($sender);

        if ($state !== null) {
            return $this->handleStateInput($sender, $state, $body);
        }

        return match ($body) {
            '1' => $this->showEventList($sender),
            '2' => $this->showFinancialSubmenu($sender),
            '3' => $this->showMenu(),
            default => null,
        };
    }

    // ────────────────────────────────────────────
    //  State Management (using Cache)
    // ────────────────────────────────────────────

    protected function getUserState(string $sender): ?array
    {
        return Cache::get(self::CACHE_PREFIX . $sender);
    }

    protected function setUserState(string $sender, array $state): void
    {
        Cache::put(self::CACHE_PREFIX . $sender, $state, now()->addMinutes(30));
    }

    protected function clearUserState(string $sender): void
    {
        Cache::forget(self::CACHE_PREFIX . $sender);
    }

    // ────────────────────────────────────────────
    //  Cek Iuran Event – multi-step
    // ────────────────────────────────────────────

    protected function showEventList(string $sender): string
    {
        $events = Event::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('active_until')
                    ->orWhere('active_until', '>=', now());
            })
            ->get();

        if ($events->isEmpty()) {
            return "📋 *Tidak ada event* yang tersedia saat ini.\n\n"
                . "Ketik */menu* untuk kembali ke menu utama.";
        }

        // Store state: user is selecting an event
        $this->setUserState($sender, [
            'step' => 'selecting_event',
        ]);

        $list = "╔═══ *DAFTAR EVENT* ═══╗\n\n"
            . "Pilih event yang ingin dicek:\n\n";

        foreach ($events as $index => $event) {
            $num = $index + 1;
            $list .= "{$num}️⃣  *{$event->name}*\n";
        }

        $list .= "\n╚════════════════════╝\n\n"
            . "Balas dengan angka *1*";

        if ($events->count() > 1) {
            $list .= "–*{$events->count()}*";
        }

        $list .= " untuk memilih event.\n\n"
            . "Ketik */menu* untuk kembali ke menu utama.";

        return $list;
    }

    protected function handleStateInput(string $sender, array $state, string $body): ?string
    {
        return match ($state['step'] ?? null) {
            'selecting_event' => $this->handleEventSelection($sender, $body),
            'entering_house' => $this->handleHouseInput($sender, $state, $body),
            'selecting_financial' => $this->handleFinancialSubmenuSelection($sender, $body),
            default => null,
        };
    }

    protected function handleEventSelection(string $sender, string $body): string
    {
        $events = Event::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('active_until')
                    ->orWhere('active_until', '>=', now());
            })
            ->get();

        $index = ((int) $body) - 1;

        if (! isset($events[$index])) {
            return "❌ Pilihan tidak valid. Silakan pilih angka yang tersedia.\n\n"
                . "Ketik */menu* untuk kembali ke menu utama.";
        }

        $event = $events[$index];

        // Store selected event
        $this->setUserState($sender, [
            'step' => 'entering_house',
            'event_id' => $event->id,
            'event_name' => $event->name,
        ]);

        return "✅ Event *{$event->name}* dipilih.\n\n"
            . "📝 Masukkan *nomor rumah* yang ingin dicek.\n"
            . "Contoh: *H8*\n\n"
            . "Ketik */menu* untuk membatalkan dan kembali ke menu utama.";
    }

    protected function handleHouseInput(string $sender, array $state, string $body): string
    {
        $houseCode = strtoupper(trim($body));
        $eventId = $state['event_id'];
        $eventName = $state['event_name'];

        if ($houseCode === '') {
            return "❌ Nomor rumah tidak boleh kosong.\n\n"
                . "Masukkan nomor rumah yang valid (contoh: *H8*).\n\n"
                . "Ketik */menu* untuk kembali ke menu utama.";
        }

        // Find house by code
        $house = House::where('code', $houseCode)->first();

        if ($house === null) {
            // Clear state so user can try again or go back to menu
            $this->clearUserState($sender);

            return "🏠 *Rumah {$houseCode}* tidak ditemukan.\n\n"
                . "Silakan periksa kembali nomor rumah Anda.\n\n"
                . "Ketik */menu* untuk kembali ke menu utama.";
        }

        // Get all contributions (payments) for this house in this event
        $contributions = EventContribution::where('event_id', $eventId)
            ->where('house_id', $house->id)
            ->get();

        // Also get EventMoneyTransaction records for this house in this event (contribution category)
        $transactions = EventMoneyTransaction::where('event_id', $eventId)
            ->where('house_id', $house->id)
            ->where('category', 'contribution')
            ->orderBy('created_at', 'desc')
            ->get();

        $hasContributions = $contributions->count() > 0 || $transactions->count() > 0;

        // Clear state after displaying result
        $this->clearUserState($sender);

        if (! $hasContributions) {
            return "╔═══ *CEK IURAN EVENT* ═══╗\n\n"
                . "📋 Event: *{$eventName}*\n"
                . "🏠 Rumah: *{$houseCode}*\n\n"
                . "━━━ *STATUS* ━━━\n"
                . "❌ *BELUM LUNAS*\n\n"
                . "Belum ada pembayaran iuran yang tercatat untuk rumah *{$houseCode}* di event *{$eventName}*.\n\n"
                . "Segera lakukan pembayaran melalui admin.\n\n"
                . "╚════════════════════════╝\n\n"
                . "Ketik */menu* untuk kembali ke menu utama.";
        }

        $paidAmount = $contributions->sum('amount') + $transactions->sum('amount');

        // Build transaction list
        $transactionLines = '';
        foreach ($transactions as $tx) {
            $amountFormatted = 'Rp ' . number_format($tx->amount, 0, ',', '.');
            $dateFormatted = $tx->created_at->format('d/m/Y');
            $desc = $tx->description ? ' · ' . $tx->description : '';
            $transactionLines .= "   • {$dateFormatted}{$desc} — *{$amountFormatted}*\n";
        }

        foreach ($contributions as $contrib) {
            $amountFormatted = 'Rp ' . number_format($contrib->amount, 0, ',', '.');
            $dateFormatted = $contrib->created_at->format('d/m/Y');
            $transactionLines .= "   • {$dateFormatted} — *{$amountFormatted}*\n";
        }

        $paidFormatted = 'Rp ' . number_format($paidAmount, 0, ',', '.');

        return "╔═══ *CEK IURAN EVENT* ═══╗\n\n"
            . "📋 Event: *{$eventName}*\n"
            . "🏠 Rumah: *{$houseCode}*\n\n"
            . "━━━ *STATUS* ━━━\n"
            . "✅ *LUNAS*\n\n"
            . "━━━ *RIWAYAT PEMBAYARAN* ━━━\n"
            . "{$transactionLines}\n"
            . "━━━ *TOTAL* ━━━\n"
            . "💰 *{$paidFormatted}*\n\n"
            . "Terima kasih sudah melakukan pembayaran! ✅\n\n"
            . "╚════════════════════════╝\n\n"
            . "Ketik */menu* untuk kembali ke menu utama.";
    }

    // ────────────────────────────────────────────
    //  Laporan Keuangan – submenu
    // ────────────────────────────────────────────

    protected function showFinancialSubmenu(string $sender): string
    {
        // Store state: user is in financial submenu
        $this->setUserState($sender, [
            'step' => 'selecting_financial',
        ]);

        return "╔═══ *LAPORAN KEUANGAN* ═══╗\n\n"
            . "Pilih laporan yang ingin dilihat:\n\n"
            . "1️⃣  *Laporan Hari Ini*\n"
            . "   Lihat transaksi hari ini\n\n"
            . "2️⃣  *Laporan Keseluruhan Event*\n"
            . "   Lihat ringkasan semua event\n\n"
            . "3️⃣  *Kembali ke Menu Utama*\n\n"
            . "╚════════════════════════╝\n\n"
            . "Balas dengan angka *1*, *2*, atau *3*";
    }

    protected function handleFinancialSubmenuSelection(string $sender, string $body): ?string
    {
        $this->clearUserState($sender);

        return match ($body) {
            '1' => $this->handleTodayReport(),
            '2' => $this->handleOverallReport(),
            '3' => $this->showMenu(),
            default => null,
        };
    }

    protected function handleTodayReport(): string
    {
        $today = now()->startOfDay();
        $tomorrow = now()->startOfDay()->addDay();

        $events = Event::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('active_until')
                    ->orWhere('active_until', '>=', now());
            })
            ->get();

        if ($events->isEmpty()) {
            return "📋 *Tidak ada event* yang tersedia saat ini.\n\n"
                . "Ketik */menu* untuk kembali ke menu utama.";
        }

        $report = "╔═══ *LAPORAN HARI INI* ═══╗\n\n"
            . "📅 Tanggal: *" . now()->format('d/m/Y') . "*\n\n";

        foreach ($events as $event) {
            $totalIncome = EventMoneyTransaction::where('event_id', $event->id)
                ->where('type', 'in')
                ->whereBetween('created_at', [$today, $tomorrow])
                ->sum('amount');

            $totalExpense = EventMoneyTransaction::where('event_id', $event->id)
                ->where('type', 'out')
                ->whereBetween('created_at', [$today, $tomorrow])
                ->sum('amount');

            $incomeCount = EventMoneyTransaction::where('event_id', $event->id)
                ->where('type', 'in')
                ->whereBetween('created_at', [$today, $tomorrow])
                ->count();

            $expenseCount = EventMoneyTransaction::where('event_id', $event->id)
                ->where('type', 'out')
                ->whereBetween('created_at', [$today, $tomorrow])
                ->count();

            $incomeFormatted = 'Rp ' . number_format($totalIncome, 0, ',', '.');
            $expenseFormatted = 'Rp ' . number_format($totalExpense, 0, ',', '.');
            $balance = $totalIncome - $totalExpense;
            $balanceFormatted = 'Rp ' . number_format($balance, 0, ',', '.');

            $report .= "━━━ *{$event->name}* ━━━\n"
                . "💰 Pemasukan: *{$incomeFormatted}* ({$incomeCount} transaksi)\n"
                . "💸 Pengeluaran: *{$expenseFormatted}* ({$expenseCount} transaksi)\n"
                . "💵 Saldo: *{$balanceFormatted}*\n\n";
        }

        $report .= "╚════════════════════════╝\n\n"
            . "Ketik */menu* untuk kembali ke menu utama.";

        return $report;
    }

    protected function handleOverallReport(): string
    {
        $events = Event::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('active_until')
                    ->orWhere('active_until', '>=', now());
            })
            ->get();

        if ($events->isEmpty()) {
            return "📋 *Tidak ada event* yang tersedia saat ini.\n\n"
                . "Ketik */menu* untuk kembali ke menu utama.";
        }

        $report = "╔═══ *LAPORAN KESELURUHAN EVENT* ═══╗\n\n";

        $grandTotalIncome = 0;
        $grandTotalExpense = 0;

        foreach ($events as $event) {
            $totalIncome = EventMoneyTransaction::where('event_id', $event->id)
                ->where('type', 'in')
                ->sum('amount');

            $totalExpense = EventMoneyTransaction::where('event_id', $event->id)
                ->where('type', 'out')
                ->sum('amount');

            $incomeCount = EventMoneyTransaction::where('event_id', $event->id)
                ->where('type', 'in')
                ->count();

            $expenseCount = EventMoneyTransaction::where('event_id', $event->id)
                ->where('type', 'out')
                ->count();

            $incomeFormatted = 'Rp ' . number_format($totalIncome, 0, ',', '.');
            $expenseFormatted = 'Rp ' . number_format($totalExpense, 0, ',', '.');
            $balance = $totalIncome - $totalExpense;
            $balanceFormatted = 'Rp ' . number_format($balance, 0, ',', '.');

            $report .= "━━━ *{$event->name}* ━━━\n"
                . "💰 Pemasukan: *{$incomeFormatted}* ({$incomeCount} transaksi)\n"
                . "💸 Pengeluaran: *{$expenseFormatted}* ({$expenseCount} transaksi)\n"
                . "💵 Saldo: *{$balanceFormatted}*\n\n";

            $grandTotalIncome += $totalIncome;
            $grandTotalExpense += $totalExpense;
        }

        // Grand total
        $grandBalance = $grandTotalIncome - $grandTotalExpense;
        $grandIncomeFormatted = 'Rp ' . number_format($grandTotalIncome, 0, ',', '.');
        $grandExpenseFormatted = 'Rp ' . number_format($grandTotalExpense, 0, ',', '.');
        $grandBalanceFormatted = 'Rp ' . number_format($grandBalance, 0, ',', '.');

        $report .= "━━━ *TOTAL KESELURUHAN* ━━━\n"
            . "💰 Pemasukan: *{$grandIncomeFormatted}*\n"
            . "💸 Pengeluaran: *{$grandExpenseFormatted}*\n"
            . "💵 Saldo: *{$grandBalanceFormatted}*\n\n";

        if ($grandBalance < 0) {
            $report .= "⚠️ *DEFISIT!* Pengeluaran melebihi pemasukan.\n\n";
        } elseif ($grandBalance > 0) {
            $report .= "✅ *SURPLUS!* Keuangan dalam kondisi baik.\n\n";
        }

        $report .= "╚════════════════════════════╝\n\n"
            . "Ketik */menu* untuk kembali ke menu utama.";

        return $report;
    }

    protected function extractPhone(string $sender): string
    {
        // Remove @s.whatsapp.net suffix if present
        if (str_contains($sender, '@')) {
            $sender = explode('@', $sender)[0];
        }

        return $sender;
    }

    protected function sendReply(GowaWebhookPayload $payload, string $reply): void
    {
        $chatId = $payload->chatId();

        if ($chatId === null) {
            return;
        }

        $this->sender->sendTextTo($chatId, $reply);
    }
}
