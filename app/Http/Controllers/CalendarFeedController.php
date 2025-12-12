<?php

namespace App\Http\Controllers;

use App\Models\Rehearsal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class CalendarFeedController extends Controller
{
    public function __invoke(Request $request, string $token): Response
    {
        $feedToken = config('calendar.feed_token');

        abort_unless(is_string($feedToken) && hash_equals($feedToken, $token), 404);

        $rehearsals = Rehearsal::query()
            ->with(['days' => fn ($query) => $query->orderBy('rehearsal_date')->orderBy('starts_at')])
            ->published()
            ->where('end_date', '>=', today())
            ->orderBy('start_date')
            ->get();

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
                $planUrl = $rehearsal->plan_path ? route('rehearsals.plan', $rehearsal, absolute: true) : null;
                $descriptionLines = collect([
                    $rehearsal->notes,
                    $day->notes,
                    $planUrl ? __('app.rehearsal_plan_pdf').': '.$planUrl : null,
                ])->filter()->implode('\\n');

                $lines[] = 'BEGIN:VEVENT';
                $lines[] = 'UID:'.$this->escape($rehearsal->ics_uid.'-'.$day->rehearsal_date->format('Ymd'));
                $lines[] = 'SUMMARY:'.$this->escape($rehearsal->title);
                $lines[] = 'DTSTART;TZID='.$rehearsal->timezone.':'.$start->format('Ymd\THis');
                $lines[] = 'DTEND;TZID='.$rehearsal->timezone.':'.$end->format('Ymd\THis');
                $lines[] = 'DTSTAMP:'.$generatedAt;
                $lines[] = 'LOCATION:'.$this->escape($location);
                $lines[] = 'DESCRIPTION:'.$this->escape($descriptionLines);
                $lines[] = 'URL;VALUE=URI:'.url(route('calendar.index', absolute: false));
                $lines[] = 'END:VEVENT';
            }
        }

        $lines[] = 'END:VCALENDAR';

        return response(implode("\r\n", $lines), 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="ensemble-calendar.ics"',
        ]);
    }

    private function escape(string $value): string
    {
        return str_replace(
            ['\\', ';', ',', "\n", "\r"],
            ['\\\\', '\;', '\,', '\\n', ''],
            $value
        );
    }
}
