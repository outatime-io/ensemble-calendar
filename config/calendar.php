<?php

return [
    'feed_token' => env('ICS_FEED_TOKEN'),
    'cache_ttl_minutes' => env('CALENDAR_CACHE_TTL_MINUTES', 30),
    'feed_ttl_minutes' => env('CALENDAR_FEED_TTL_MINUTES', 30),
    'feed_plan_in_description' => env('CALENDAR_FEED_PLAN_IN_DESCRIPTION', true),
];
