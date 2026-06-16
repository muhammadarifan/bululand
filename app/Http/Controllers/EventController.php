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
        if ($eventDetail && $eventDetail->logo) {
            $eventDetail->logo = Storage::disk('local')->temporaryUrl(
                $eventDetail->logo,
                now()->addHours(1)
            );
        }
        if ($eventDetail && $eventDetail->hero_image) {
            $eventDetail->hero_image = Storage::disk('local')->temporaryUrl(
                $eventDetail->hero_image,
                now()->addHours(1)
            );
        }

        $searchQuery = $request->query('search', '');

        // Search for contributions by house name from money transactions
        $contributionResults = [];
        if ($searchQuery !== '') {
            $searchLower = Str::lower($searchQuery);

            $houses = House::whereRaw('LOWER(code) LIKE ?', ["%{$searchLower}%"])->get();

            foreach ($houses as $house) {
                $paidAmount = EventMoneyTransaction::where('event_id', $eventModel->id)
                    ->where('house_id', $house->id)
                    ->where('type', 'in')
                    ->sum('amount');

                $contributionResults[] = [
                    'name' => $house->code,
                    'paid_amount' => $paidAmount,
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
}
