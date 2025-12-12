<?php

namespace App\Http\Controllers;

use App\Models\Rehearsal;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        $rehearsals = Rehearsal::query()
            ->with(['days' => fn ($query) => $query->orderBy('rehearsal_date')->orderBy('starts_at')])
            ->published()
            ->where('end_date', '>=', today())
            ->orderBy('start_date')
            ->get();

        $feedToken = config('calendar.feed_token');
        $feedUrl = $feedToken ? route('calendar.feed', ['token' => $feedToken]) : null;

        return view('calendar.index', [
            'rehearsals' => $rehearsals,
            'feedUrl' => $feedUrl,
        ]);
    }
}
