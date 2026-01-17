<?php

namespace App\Http\Controllers;

use App\Models\Rehearsal;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        $rehearsals = Rehearsal::upcomingCached();

        $feedToken = config('calendar.feed_token');
        $feedUrl = $feedToken ? route('calendar.feed', ['token' => $feedToken]) : null;

        return view('calendar.index', [
            'rehearsals' => $rehearsals,
            'feedUrl' => $feedUrl,
        ]);
    }
}
