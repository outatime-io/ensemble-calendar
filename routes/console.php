<?php

use App\Http\Controllers\CalendarFeedController;
use App\Models\Rehearsal;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('calendar:cache', function () {
    calendar_bump_cache_version();

    $rehearsals = Rehearsal::publishedCached();
    $feed = CalendarFeedController::renderFeed($rehearsals);
    $ttlMinutes = (int) config('calendar.feed_ttl_minutes', 60);

    Cache::put(calendar_feed_cache_key(), $feed, now()->addMinutes($ttlMinutes));

    $this->info('Calendar cache warmed.');
})->purpose('Warm upcoming rehearsal and feed caches');
