<?php

namespace App\Http\Controllers;

use App\Models\Rehearsal;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $view = $request->query('view', 'upcoming');
        $allowedViews = ['upcoming', 'past', 'all'];

        if (! in_array($view, $allowedViews, true)) {
            $view = 'upcoming';
        }

        $rehearsals = match ($view) {
            'past' => Rehearsal::pastPublishedCached(),
            'all' => Rehearsal::publishedCached(),
            default => Rehearsal::upcomingCached(),
        };

        $feedToken = config('calendar.feed_token');
        $feedUrl = $feedToken ? route('calendar.feed', ['token' => $feedToken]) : null;

        return view('calendar.index', [
            'rehearsals' => $rehearsals,
            'feedUrl' => $feedUrl,
            'view' => $view,
        ]);
    }
}
