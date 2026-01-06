<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberEventsController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);

        $date = Carbon::createFromDate($year, $month, 1);
        $start = $date->copy()->startOfMonth()->startOfDay();
        $end = $date->copy()->endOfMonth()->endOfDay();

        $events = Event::query()
            ->whereBetween('start_date', [$start, $end])
            ->orWhereBetween('end_date', [$start, $end])
            ->orderBy('start_date')
            ->get();

        return Inertia::render('Member/Events', [
            'events' => $events,
            'currentDate' => $date->format('Y-m-d'),
        ]);
    }
}


