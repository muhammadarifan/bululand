<?php

namespace App\Services;

use App\DTO\GowaWebhookPayload;
use App\Models\Event;
use App\Models\EventContribution;
use App\Models\EventMoneyTransaction;
use App\Models\House;
use App\Models\WhatsappWhitelist;
use App\Services\Gowa\GowaMessageSender;
use Illuminate\Support\Facades\Log;

use function data_get;

class AutoReplyGowaWebhookService
{
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

        Log::info('Auto reply for message', [
            'message' => $message,
        ]);

        if (! is_array($message)) {
            return null;
        }

        $body = data_get($message, 'body');

        if (! is_string($body)) {
            return null;
        }

        $body = trim($body);
        $sender = $payload->sender();

        // Handle /menu command (membutuhkan sender)
        if ($sender !== null && strtolower($body) === '/menu') {
            return $this->showMenu();
        }

        // Handle menu selection (1, 2, 3) - membutuhkan sender
        if ($sender !== null) {
            $menuReply = $this->handleMenuSelection($sender, $body);

            if ($menuReply !== null) {
                return $menuReply;
            }
        }

        // Fallback: sapa balik jika ada kata "halo"
        if (str_contains(strtolower($body), 'halo')) {
            return 'Halo juga!';
        }

        return null;
    }

    protected function showMenu(): string
    {
        return "╔═══ *MENU BOT GOWA* ═══╗\n\n"
            . "1️⃣  *Cek Pembayaran Event*\n"
            . "   Cek apakah kamu sudah membayar iuran event atau belum\n\n"
            . "2️⃣  *Laporan Keuangan Event*\n"
            . "   Lihat laporan pemasukan & pengeluaran event\n\n"
            . "3️⃣  *Kembali ke Menu Awal*\n\n"
            . "╚════════════════════╝\n\n"
            . "Balas dengan angka *1*, *2*, atau *3*";
    }

    protected function handleMenuSelection(string $sender, string $body): ?string
    {
        $activeEvent = Event::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('active_until')
                    ->orWhere('active_until', '>=', now());
            })
            ->first();

        if ($activeEvent === null) {
            return null;
        }

        return match ($body) {
            '1' => $this->handleCheckPayment($sender, $activeEvent),
            '2' => $this->handleFinancialReport($activeEvent),
            '3' => $this->showMenu(),
            default => null,
        };
    }

    protected function handleCheckPayment(string $sender, Event $event): string
    {
        // Extract phone number from sender (format: 62812...@s.whatsapp.net)
        $phone = $this->extractPhone($sender);

        // Try to find house by phone number in whitelist
        $whitelist = WhatsappWhitelist::where('phone', $phone)->first();

        if ($whitelist === null) {
            return "📱 *Nomor {$phone}* tidak terdaftar.\n\n"
                . "Silahkan hubungi admin untuk mendaftarkan nomor WhatsApp kamu.\n\n"
                . "Ketik */menu* untuk kembali ke menu utama.";
        }

        // Check if there's a house with matching code/phone
        $house = House::where('code', $phone)->first();

        // If no direct house code match, try to find by EventContribution house relation
        if ($house === null) {
            return "🏠 *Tidak ada rumah* yang terdaftar dengan nomor WhatsApp ini.\n\n"
                . "Hubungi admin untuk informasi lebih lanjut.\n\n"
                . "Ketik */menu* untuk kembali ke menu utama.";
        }

        // Check contribution status
        $contribution = EventContribution::where('event_id', $event->id)
            ->where('house_id', $house->id)
            ->first();

        if ($contribution === null) {
            return "❌ *BELUM BAYAR*\n\n"
                . "Rumah *{$house->code}* belum tercatat melakukan pembayaran untuk event *{$event->name}*.\n\n"
                . "Segera lakukan pembayaran melalui admin.\n\n"
                . "Ketik */menu* untuk kembali ke menu utama.";
        }

        $amountFormatted = 'Rp ' . number_format($contribution->amount, 0, ',', '.');

        return "✅ *SUDAH BAYAR*\n\n"
            . "🏠 Rumah: *{$house->code}*\n"
            . "📋 Event: *{$event->name}*\n"
            . "💰 Jumlah: *{$amountFormatted}*\n"
            . "📅 Tanggal: *{$contribution->created_at->format('d/m/Y H:i')}*\n\n"
            . "Terima kasih sudah melakukan pembayaran! ✅\n\n"
            . "Ketik */menu* untuk kembali ke menu utama.";
    }

    protected function handleFinancialReport(Event $event): string
    {
        // Calculate total income (type = 'in')
        $totalIncome = EventMoneyTransaction::where('event_id', $event->id)
            ->where('type', 'in')
            ->sum('amount');

        // Calculate total expense (type = 'out')
        $totalExpense = EventMoneyTransaction::where('event_id', $event->id)
            ->where('type', 'out')
            ->sum('amount');

        // Count transactions
        $incomeCount = EventMoneyTransaction::where('event_id', $event->id)
            ->where('type', 'in')
            ->count();

        $expenseCount = EventMoneyTransaction::where('event_id', $event->id)
            ->where('type', 'out')
            ->count();

        $balance = $totalIncome - $totalExpense;

        $incomeFormatted = 'Rp ' . number_format($totalIncome, 0, ',', '.');
        $expenseFormatted = 'Rp ' . number_format($totalExpense, 0, ',', '.');
        $balanceFormatted = 'Rp ' . number_format($balance, 0, ',', '.');

        $report = "╔═══ *LAPORAN KEUANGAN* ═══╗\n\n"
            . "📋 Event: *{$event->name}*\n\n"
            . "━━━ *PEMASUKAN* ━━━\n"
            . "💰 Total: *{$incomeFormatted}*\n"
            . "📊 Transaksi: *{$incomeCount}* kali\n\n"
            . "━━━ *PENGELUARAN* ━━━\n"
            . "💸 Total: *{$expenseFormatted}*\n"
            . "📊 Transaksi: *{$expenseCount}* kali\n\n"
            . "━━━ *SALDO AKHIR* ━━━\n"
            . "💵 *{$balanceFormatted}*\n\n";

        if ($balance < 0) {
            $report .= "⚠️ *DEFISIT!* Pengeluaran melebihi pemasukan.\n\n";
        } elseif ($balance > 0) {
            $report .= "✅ *SURPLUS!* Keuangan dalam kondisi baik.\n\n";
        }

        $report .= "╚════════════════════════╝\n\n"
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
