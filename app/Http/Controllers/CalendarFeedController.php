<?php

namespace App\Http\Controllers;

use App\Models\Rehearsal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CalendarFeedController extends Controller
{
    public function __invoke(Request $request, string $token): Response
    {
        $feedToken = config('calendar.feed_token');

        abort_unless(is_string($feedToken) && hash_equals($feedToken, $token), 404);

        $ttlMinutes = (int) config('calendar.feed_ttl_minutes', 60);
        $feed = Cache::remember(
            calendar_feed_cache_key(),
            now()->addMinutes($ttlMinutes),
            function (): string {
                $rehearsals = Rehearsal::upcomingCached();

                return self::renderFeed($rehearsals);
            }
        );

        return response($feed, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="ensemble-calendar.ics"',
        ]);
    }

    public static function renderFeed(Collection $rehearsals): string
    {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Ensemble Calendar//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:Ensemble Proben',
            'X-WR-TIMEZONE:'.config('app.timezone'),
        ];

        $generatedAt = Carbon::now('UTC')->format('Ymd\THis\Z');

        foreach ($rehearsals as $rehearsal) {
            foreach ($rehearsal->days as $day) {
                $start = $day->startDateTime($rehearsal->timezone);
                $end = $day->endDateTime($rehearsal->timezone);
                $location = trim($rehearsal->location_name.($rehearsal->location_address ? ', '.$rehearsal->location_address : ''));
                $feedToken = config('calendar.feed_token');
                $planUrl = $rehearsal->plan_path
                    ? route('rehearsals.plan', ['rehearsal' => $rehearsal, 'token' => $feedToken], absolute: true)
                    : null;
                $descriptionLines = collect([
                    $rehearsal->notes,
                    config('calendar.feed_plan_in_description') && $planUrl
                        ? __('app.rehearsal_plan_pdf').': '.$planUrl
                        : null,
                ])->filter()->implode('\\n');

                $lines[] = 'BEGIN:VEVENT';
                $lines[] = 'UID:'.self::escape($rehearsal->ics_uid.'-'.$day->rehearsal_date->format('Ymd'));
                $lines[] = 'SUMMARY:'.self::escape($rehearsal->title);
                $lines[] = 'DTSTART;TZID='.$rehearsal->timezone.':'.$start->format('Ymd\THis');
                $lines[] = 'DTEND;TZID='.$rehearsal->timezone.':'.$end->format('Ymd\THis');
                $lines[] = 'DTSTAMP:'.$generatedAt;
                $lines[] = 'LOCATION:'.self::escape($location);
                $lines[] = 'DESCRIPTION:'.self::escape($descriptionLines);
                if ($planUrl) {
                    $lines[] = 'ATTACH;VALUE=URI:'.$planUrl;
                }
                $lines[] = 'URL;VALUE=URI:'.route('calendar.index', absolute: true);
                $lines[] = 'END:VEVENT';
            }
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines);
    }

    private static function escape(string $value): string
    {
        return str_replace(
            ['\\', ';', ',', "\n", "\r"],
            ['\\\\', '\;', '\,', '\\n', ''],
            $value
        );
    }
}
