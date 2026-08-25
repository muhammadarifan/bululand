<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventDebt;
use App\Models\EventItemDonation;
use App\Models\EventMoneyTransaction;
use App\Models\House;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function show(Request $request, $event = null)
    {
        $eventModel = Event::where('subdomain', $event)->first();

        if (! $eventModel) {
            abort(404, 'Event tidak ditemukan.');
        }

        // Check if the event is active
        if (! $eventModel->is_active) {
            abort(404, 'Event ini tidak aktif.');
        }

        // Check if the event has expired
        if ($eventModel->active_until && $eventModel->active_until->isPast()) {
            abort(404, 'Event ini sudah berakhir.');
        }

        $eventDetail = $eventModel->eventDetail;

        // Resolve file URLs from private storage
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('local');
        if ($eventDetail && $eventDetail->logo) {
            $eventDetail->logo = $storage->temporaryUrl($eventDetail->logo, 60);
        }
        if ($eventDetail && $eventDetail->hero_image) {
            $eventDetail->hero_image = $storage->temporaryUrl($eventDetail->hero_image, 60);
        }

        $searchQuery = $request->query('search', '');

        // Search for contributions by house name from money transactions
        $contributionResults = [];
        if ($searchQuery !== '') {
            $searchLower = Str::lower($searchQuery);

            $houses = House::whereRaw('LOWER(code) LIKE ?', ["%{$searchLower}%"])->get();

            foreach ($houses as $house) {
                $transactions = EventMoneyTransaction::where('event_id', $eventModel->id)
                    ->where('house_id', $house->id)
                    ->where('type', 'in')
                    ->orderBy('created_at', 'asc')
                    ->get(['amount', 'created_at', 'description']);

                $paidAmount = $transactions->sum('amount');

                $contributionResults[] = [
                    'name' => $house->code,
                    'paid_amount' => $paidAmount,
                    'transactions' => $transactions->map(function ($tx) {
                        return [
                            'amount' => $tx->amount,
                            'date' => $tx->created_at->format('d M Y'),
                            'description' => $tx->description,
                        ];
                    }),
                ];
            }

            if (empty($contributionResults)) {
                $contributionResults = ['not_found' => true, 'query' => $searchQuery];
            }
        }

        $totalIncome = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->sum('amount');

        $totalExpense = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'out')
            ->sum('amount');

        $totalItemDonation = EventItemDonation::where('event_id', $eventModel->id)
            ->selectRaw('SUM(COALESCE(price, 0) * quantity) as total')
            ->value('total') ?? 0;

        $debts = EventDebt::where('event_id', $eventModel->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $totalDebt = $debts->sum('amount');

        return view('events.show', [
            'event' => $eventModel,
            'eventDetail' => $eventDetail,
            'searchQuery' => $searchQuery,
            'contributionResults' => $contributionResults,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalItemDonation' => $totalItemDonation,
            'debts' => $debts,
            'totalDebt' => $totalDebt,
        ]);
    }

    public function print(Request $request, $event = null)
    {
        $eventModel = Event::where('subdomain', $event)->first();

        if (! $eventModel) {
            abort(404, 'Event tidak ditemukan.');
        }

        if (! $eventModel->is_active) {
            abort(404, 'Event ini tidak aktif.');
        }

        if ($eventModel->active_until && $eventModel->active_until->isPast()) {
            abort(404, 'Event ini sudah berakhir.');
        }

        $totalIncome = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->sum('amount');

        $totalExpense = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'out')
            ->sum('amount');

        $totalItemDonation = EventItemDonation::where('event_id', $eventModel->id)
            ->selectRaw('SUM(COALESCE(price, 0) * quantity) as total')
            ->value('total') ?? 0;

        $iuranTransactions = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->where('category', 'contribution')
            ->with('house')
            ->orderBy('created_at', 'desc')
            ->get();

        $donasiTransactions = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->where(function ($q) {
                $q->where('category', '!=', 'contribution')
                    ->orWhereNull('category');
            })
            ->with('house')
            ->orderBy('amount', 'desc')
            ->get();

        $expenseTransactions = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'out')
            ->with('house')
            ->orderBy('created_at', 'desc')
            ->get();

        $anonymousNumbers = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('is_anonymous', true)
            ->orderBy('id')
            ->pluck('id')
            ->flip()
            ->map(fn ($index) => $index + 1);

        $itemDonations = EventItemDonation::where('event_id', $eventModel->id)
            ->with('house')
            ->orderBy('created_at', 'desc')
            ->get();

        $anonymousItemNumbers = EventItemDonation::where('event_id', $eventModel->id)
            ->where('is_anonymous', true)
            ->orderBy('id')
            ->pluck('id')
            ->flip()
            ->map(fn ($index) => $index + 1);

        $moneyByHouse = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->whereNotNull('house_id')
            ->selectRaw("house_id, SUM(CASE WHEN category = 'contribution' THEN amount ELSE 0 END) as contribution_total, SUM(CASE WHEN category != 'contribution' OR category IS NULL THEN amount ELSE 0 END) as donation_total")
            ->groupBy('house_id')
            ->get()
            ->keyBy('house_id');

        $itemByHouse = EventItemDonation::where('event_id', $eventModel->id)
            ->whereNotNull('house_id')
            ->selectRaw('house_id, SUM(COALESCE(price, 0) * quantity) as item_total')
            ->groupBy('house_id')
            ->get()
            ->keyBy('house_id');

        $houseIds = $moneyByHouse->keys()->merge($itemByHouse->keys())->unique();
        $houses = House::whereIn('id', $houseIds)->get()->keyBy('id');

        $houseRecap = $houseIds->map(function ($houseId) use ($moneyByHouse, $itemByHouse, $houses) {
            $contributionTotal = $moneyByHouse[$houseId]->contribution_total ?? 0;
            $donationTotal = $moneyByHouse[$houseId]->donation_total ?? 0;
            $itemTotal = $itemByHouse[$houseId]->item_total ?? 0;

            return (object) [
                'house' => $houses[$houseId] ?? null,
                'contribution_total' => $contributionTotal,
                'donation_total' => $donationTotal,
                'item_total' => $itemTotal,
                'grand_total' => $contributionTotal + $donationTotal + $itemTotal,
            ];
        })->sortByDesc('grand_total')->values();

        return view('events.print', [
            'event' => $eventModel,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalItemDonation' => $totalItemDonation,
            'houseRecap' => $houseRecap,
            'iuranTransactions' => $iuranTransactions,
            'donasiTransactions' => $donasiTransactions,
            'expenseTransactions' => $expenseTransactions,
            'anonymousNumbers' => $anonymousNumbers,
            'itemDonations' => $itemDonations,
            'anonymousItemNumbers' => $anonymousItemNumbers,
        ]);
    }

    public function transactions(Request $request, $event = null)
    {
        $eventModel = Event::where('subdomain', $event)->first();

        if (! $eventModel) {
            abort(404, 'Event tidak ditemukan.');
        }

        if (! $eventModel->is_active) {
            abort(404, 'Event ini tidak aktif.');
        }

        if ($eventModel->active_until && $eventModel->active_until->isPast()) {
            abort(404, 'Event ini sudah berakhir.');
        }

        $eventDetail = $eventModel->eventDetail;

        // Resolve file URLs from private storage
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('local');
        if ($eventDetail && $eventDetail->logo) {
            $eventDetail->logo = $storage->temporaryUrl($eventDetail->logo, 60);
        }

        $totalIncome = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->sum('amount');

        $totalExpense = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'out')
            ->sum('amount');

        $totalItemDonation = EventItemDonation::where('event_id', $eventModel->id)
            ->selectRaw('SUM(COALESCE(price, 0) * quantity) as total')
            ->value('total') ?? 0;

        // Contribution total
        $totalContribution = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->where('category', 'contribution')
            ->sum('amount');

        $debts = EventDebt::where('event_id', $eventModel->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $totalDebt = $debts->sum('amount');

        // Iuran wajib (contribution), grouped by house, only merging rows
        // that share the same house AND the same is_anonymous value. Rows
        // without a house are never merged with each other (each keeps its
        // own group via id).
        $iuranByHouse = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->where('category', 'contribution')
            ->selectRaw('house_id, is_anonymous, donor_name, MIN(id) as first_id, SUM(amount) as total_amount')
            ->with('house')
            ->groupBy(DB::raw("COALESCE(house_id::text, CONCAT('anon_', id))"))
            ->groupBy('house_id')
            ->groupBy('is_anonymous')
            ->groupBy('donor_name')
            ->orderByDesc('total_amount')
            ->get();

        $iuranTransactions = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->where('category', 'contribution')
            ->with('house')
            ->orderBy('created_at', 'desc')
            ->get();

        $donasiTransactions = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->where(function ($q) {
                $q->where('category', '!=', 'contribution')
                    ->orWhereNull('category');
            })
            ->with('house')
            ->orderBy('amount', 'desc')
            ->get();

        $expenseTransactions = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'out')
            ->with('house')
            ->orderBy('created_at', 'desc')
            ->get();

        // Sequential number for each anonymous donor, ordered by id, so the
        // same transaction always resolves to the same "Orang Baik {n}" label.
        $anonymousNumbers = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('is_anonymous', true)
            ->orderBy('id')
            ->pluck('id')
            ->flip()
            ->map(fn ($index) => $index + 1);

        $itemDonations = EventItemDonation::where('event_id', $eventModel->id)
            ->with('house')
            ->orderBy('created_at', 'desc')
            ->get();

        // Separate anonymous numbering sequence for item donations.
        $anonymousItemNumbers = EventItemDonation::where('event_id', $eventModel->id)
            ->where('is_anonymous', true)
            ->orderBy('id')
            ->pluck('id')
            ->flip()
            ->map(fn ($index) => $index + 1);

        // Rekap sumbangan per rumah: iuran wajib + donasi uang + donasi
        // barang bernominal, dijumlahkan per rumah (donasi tanpa rumah,
        // mis. anonim, tidak masuk rekap ini).
        $moneyByHouse = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->whereNotNull('house_id')
            ->selectRaw("house_id, SUM(CASE WHEN category = 'contribution' THEN amount ELSE 0 END) as contribution_total, SUM(CASE WHEN category != 'contribution' OR category IS NULL THEN amount ELSE 0 END) as donation_total")
            ->groupBy('house_id')
            ->get()
            ->keyBy('house_id');

        $itemByHouse = EventItemDonation::where('event_id', $eventModel->id)
            ->whereNotNull('house_id')
            ->selectRaw('house_id, SUM(COALESCE(price, 0) * quantity) as item_total')
            ->groupBy('house_id')
            ->get()
            ->keyBy('house_id');

        $houseIds = $moneyByHouse->keys()->merge($itemByHouse->keys())->unique();
        $houses = House::whereIn('id', $houseIds)->get()->keyBy('id');

        $houseRecap = $houseIds->map(function ($houseId) use ($moneyByHouse, $itemByHouse, $houses) {
            $contributionTotal = $moneyByHouse[$houseId]->contribution_total ?? 0;
            $donationTotal = $moneyByHouse[$houseId]->donation_total ?? 0;
            $itemTotal = $itemByHouse[$houseId]->item_total ?? 0;

            return (object) [
                'house' => $houses[$houseId] ?? null,
                'contribution_total' => $contributionTotal,
                'donation_total' => $donationTotal,
                'item_total' => $itemTotal,
                'grand_total' => $contributionTotal + $donationTotal + $itemTotal,
            ];
        })->sortByDesc('grand_total')->values();

        // Search house contribution
        $searchHouse = $request->query('search_house', '');
        $houseResult = [];
        if ($searchHouse !== '') {
            $house = House::whereRaw('LOWER(code) LIKE ?', ['%'.Str::lower($searchHouse).'%'])->first();

            if ($house) {
                $transactions = EventMoneyTransaction::where('event_id', $eventModel->id)
                    ->where('house_id', $house->id)
                    ->where('type', 'in')
                    ->where('category', 'contribution')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $total = $transactions->sum('amount');

                $houseResult = [
                    'house_code' => $house->code,
                    'total' => $total,
                    'count' => $transactions->count(),
                    'transactions' => $transactions,
                ];
            } else {
                $houseResult = ['not_found' => true];
            }
        }

        return view('events.transactions', [
            'event' => $eventModel,
            'eventDetail' => $eventDetail,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalItemDonation' => $totalItemDonation,
            'totalContribution' => $totalContribution,
            'debts' => $debts,
            'totalDebt' => $totalDebt,
            'houseRecap' => $houseRecap,
            'iuranByHouse' => $iuranByHouse,
            'iuranTransactions' => $iuranTransactions,
            'donasiTransactions' => $donasiTransactions,
            'expenseTransactions' => $expenseTransactions,
            'anonymousNumbers' => $anonymousNumbers,
            'itemDonations' => $itemDonations,
            'anonymousItemNumbers' => $anonymousItemNumbers,
            'searchHouse' => $searchHouse,
            'houseResult' => $houseResult,
        ]);
    }

    public function checkContribution(Request $request, $event = null)
    {
        $eventModel = Event::where('subdomain', $event)->first();

        if (! $eventModel) {
            return response()->json(['error' => 'Event tidak ditemukan.'], 404);
        }

        if (! $eventModel->is_active) {
            return response()->json(['error' => 'Event ini tidak aktif.'], 404);
        }

        if ($eventModel->active_until && $eventModel->active_until->isPast()) {
            return response()->json(['error' => 'Event ini sudah berakhir.'], 404);
        }

        $eventDetail = $eventModel->eventDetail;
        $searchQuery = $request->query('search', '');

        if ($searchQuery === '') {
            return response()->json(['error' => 'Nama rumah harus diisi.'], 422);
        }

        $searchLower = Str::lower($searchQuery);
        $house = House::whereRaw('LOWER(code) = ?', [$searchLower])->first();

        if (! $house) {
            return response()->json([
                'found' => false,
                'message' => 'Rumah dengan kode "'.$searchQuery.'" tidak ditemukan.',
            ], 200);
        }

        $transactions = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('house_id', $house->id)
            ->where('type', 'in')
            ->where('category', 'contribution')
            ->orderBy('created_at', 'asc')
            ->get(['amount', 'created_at']);

        $totalPaid = $transactions->sum('amount');
        $contributionFee = $eventDetail ? (float) $eventDetail->contribution_fee : 0;
        $isPaid = $totalPaid >= $contributionFee;

        $transactionData = $transactions->map(function ($tx) {
            return [
                'amount' => (float) $tx->amount,
                'date' => $tx->created_at->format('d M Y'),
            ];
        });

        return response()->json([
            'found' => true,
            'house_code' => $house->code,
            'total_paid' => $totalPaid,
            'contribution_fee' => $contributionFee,
            'is_paid' => $isPaid,
            'transactions' => $transactionData,
        ]);
    }

    public function unpaidContributions(Request $request, $event = null)
    {
        $eventModel = Event::where('subdomain', $event)->first();

        if (! $eventModel) {
            abort(404, 'Event tidak ditemukan.');
        }

        if (! $eventModel->is_active) {
            abort(404, 'Event ini tidak aktif.');
        }

        if ($eventModel->active_until && $eventModel->active_until->isPast()) {
            abort(404, 'Event ini sudah berakhir.');
        }

        $eventDetail = $eventModel->eventDetail;

        if ($eventDetail && $eventDetail->logo) {
            /** @var FilesystemAdapter $storage */
            $storage = Storage::disk('local');
            $eventDetail->logo = $storage->temporaryUrl($eventDetail->logo, 60);
        }

        $sessionKey = 'unpaid_contributions_unlocked_'.$eventModel->id;
        $unlocked = (bool) session($sessionKey);

        if (! $unlocked) {
            return view('events.unpaid-contributions-locked', [
                'event' => $eventModel,
                'eventDetail' => $eventDetail,
            ]);
        }

        $contributionFee = (float) ($eventDetail->contribution_fee ?? 0);

        $paidTotals = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->where('category', 'contribution')
            ->selectRaw('house_id, SUM(amount) as total_paid')
            ->groupBy('house_id')
            ->pluck('total_paid', 'house_id');

        $unpaidHouses = House::orderBy('code')->get()
            ->map(function (House $house) use ($paidTotals, $contributionFee) {
                $paid = (float) ($paidTotals[$house->id] ?? 0);

                return [
                    'code' => $house->code,
                    'paid' => $paid,
                    'remaining' => max($contributionFee - $paid, 0),
                ];
            })
            ->filter(fn (array $house): bool => $house['paid'] < $contributionFee)
            ->values();

        return view('events.unpaid-contributions', [
            'event' => $eventModel,
            'eventDetail' => $eventDetail,
            'contributionFee' => $contributionFee,
            'unpaidHouses' => $unpaidHouses,
        ]);
    }

    public function unlockUnpaidContributions(Request $request, $event = null)
    {
        $eventModel = Event::where('subdomain', $event)->first();

        if (! $eventModel) {
            abort(404, 'Event tidak ditemukan.');
        }

        $throttleKey = 'unpaid-contrib-unlock:'.$eventModel->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'access_code' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $request->validate([
            'access_code' => ['required', 'string'],
        ]);

        $eventDetail = $eventModel->eventDetail;
        $storedHash = $eventDetail?->unpaid_contribution_access_code;

        if (! $storedHash || ! Hash::check($request->input('access_code'), $storedHash)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'access_code' => 'Kode akses salah.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->put('unpaid_contributions_unlocked_'.$eventModel->id, true);

        return redirect()->route('events.unpaid-contributions', $eventModel->subdomain);
    }
}
