<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventMoneyTransaction;
use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function show(Request $request, $event = null)
    {
        $eventModel = Event::where('subdomain', $event)->first();

        if (!$eventModel) {
            abort(404, 'Event tidak ditemukan.');
        }

        // Check if the event is active
        if (!$eventModel->is_active) {
            abort(404, 'Event ini tidak aktif.');
        }

        // Check if the event has expired
        if ($eventModel->active_until && $eventModel->active_until->isPast()) {
            abort(404, 'Event ini sudah berakhir.');
        }

        $eventDetail = $eventModel->eventDetail;

        // Resolve file URLs from private storage
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
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

        return view('events.show', [
            'event' => $eventModel,
            'eventDetail' => $eventDetail,
            'searchQuery' => $searchQuery,
            'contributionResults' => $contributionResults,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
        ]);
    }

    public function transactions(Request $request, $event = null)
    {
        $eventModel = Event::where('subdomain', $event)->first();

        if (!$eventModel) {
            abort(404, 'Event tidak ditemukan.');
        }

        if (!$eventModel->is_active) {
            abort(404, 'Event ini tidak aktif.');
        }

        if ($eventModel->active_until && $eventModel->active_until->isPast()) {
            abort(404, 'Event ini sudah berakhir.');
        }

        $eventDetail = $eventModel->eventDetail;

        // Resolve file URLs from private storage
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
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

        // Contribution total
        $totalContribution = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->where('category', 'contribution')
            ->sum('amount');

        $incomeTransactions = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'in')
            ->where(function ($q) {
                $q->where('category', '!=', 'contribution')
                    ->orWhereNull('category');
            })
            ->with('house')
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'income_page');

        $expenseTransactions = EventMoneyTransaction::where('event_id', $eventModel->id)
            ->where('type', 'out')
            ->with('house')
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'expense_page');

        // Search house contribution
        $searchHouse = $request->query('search_house', '');
        $houseResult = [];
        if ($searchHouse !== '') {
            $house = House::whereRaw('LOWER(code) LIKE ?', ['%' . Str::lower($searchHouse) . '%'])->first();

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
            'totalContribution' => $totalContribution,
            'incomeTransactions' => $incomeTransactions,
            'expenseTransactions' => $expenseTransactions,
            'searchHouse' => $searchHouse,
            'houseResult' => $houseResult,
        ]);
    }

    public function checkContribution(Request $request, $event = null)
    {
        $eventModel = Event::where('subdomain', $event)->first();

        if (!$eventModel) {
            return response()->json(['error' => 'Event tidak ditemukan.'], 404);
        }

        if (!$eventModel->is_active) {
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

        if (!$house) {
            return response()->json([
                'found' => false,
                'message' => 'Rumah dengan kode "' . $searchQuery . '" tidak ditemukan.',
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
}
