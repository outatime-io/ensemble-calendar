<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('calendar_cache_version')) {
    function calendar_cache_version(): int
    {
        return (int) cache()->get('calendar.cache_version', 1);
    }
}

if (! function_exists('calendar_bump_cache_version')) {
    function calendar_bump_cache_version(): int
    {
        $next = (int) cache()->get('calendar.cache_version', 0) + 1;

        cache()->forever('calendar.cache_version', $next);

        return $next;
    }
}

if (! function_exists('calendar_rehearsals_cache_key')) {
    function calendar_rehearsals_cache_key(): string
    {
        return 'calendar.upcoming.v'.calendar_cache_version();
    }
}

if (! function_exists('calendar_feed_rehearsals_cache_key')) {
    function calendar_feed_rehearsals_cache_key(): string
    {
        return 'calendar.feed-rehearsals.v'.calendar_cache_version();
    }
}

if (! function_exists('calendar_feed_cache_key')) {
    function calendar_feed_cache_key(): string
    {
        return 'calendar.feed.v'.calendar_cache_version();
    }
}
