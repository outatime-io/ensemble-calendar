<?php

namespace Database\Seeders;

use App\Models\Rehearsal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class RehearsalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();
        $timezone = config('app.timezone');

        $singleDates = [
            $today->copy()->subDays(12),
            $today->copy()->addDays(6),
            $today->copy()->addDays(20),
        ];

        foreach ($singleDates as $date) {
            $this->seedRehearsal(
                $this->singleDayTitle($date),
                $timezone,
                [
                    [
                        'date' => $date,
                        'starts_at' => '18:30',
                        'ends_at' => '21:30',
                        'notes' => __('app.rehearsal_seed_notes_single'),
                    ],
                ],
                __('app.rehearsal_location_main')
            );
        }

        $pastWeekend = $today->copy()->previous(Carbon::SATURDAY)->subWeeks(3);
        $firstWeekend = $today->copy()->next(Carbon::SATURDAY);
        $secondWeekend = $firstWeekend->copy()->addWeeks(5);

        $this->seedWeekend($pastWeekend, $timezone, __('app.rehearsal_location_church'));
        $this->seedWeekend($firstWeekend, $timezone, __('app.rehearsal_location_church'));
        $this->seedWeekend($secondWeekend, $timezone, __('app.rehearsal_location_annex'), true);
    }

    private function seedWeekend(Carbon $start, string $timezone, string $location, bool $threeDays = false): void
    {
        $days = [
            [
                'date' => $start,
                'starts_at' => '10:00',
                'ends_at' => '17:00',
                'notes' => __('app.rehearsal_seed_notes_weekend'),
            ],
            [
                'date' => $start->copy()->addDay(),
                'starts_at' => '10:00',
                'ends_at' => '16:00',
                'notes' => __('app.rehearsal_seed_notes_weekend'),
            ],
        ];

        if ($threeDays) {
            $days[] = [
                'date' => $start->copy()->addDays(2),
                'starts_at' => '10:00',
                'ends_at' => '14:00',
                'notes' => __('app.rehearsal_seed_notes_weekend'),
            ];
        }

        $this->seedRehearsal(
            $this->weekendTitle($start, $start->copy()->addDays($threeDays ? 2 : 1)),
            $timezone,
            $days,
            $location
        );
    }

    /**
     * @param  array<int, array{date: Carbon, starts_at: string, ends_at: string, notes: string|null}>  $days
     */
    private function seedRehearsal(string $title, string $timezone, array $days, string $location): void
    {
        $startDate = $days[0]['date']->copy();
        $endDate = $days[0]['date']->copy();

        foreach ($days as $day) {
            if ($day['date']->lt($startDate)) {
                $startDate = $day['date']->copy();
            }

            if ($day['date']->gt($endDate)) {
                $endDate = $day['date']->copy();
            }
        }

        $rehearsal = Rehearsal::query()->create([
            'title' => $title,
            'location_name' => $location,
            'location_address' => __('app.rehearsal_seed_address'),
            'notes' => __('app.rehearsal_seed_notes_overview'),
            'timezone' => $timezone,
            'is_published' => true,
            'ics_uid' => (string) Str::uuid(),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        foreach ($days as $day) {
            $rehearsal->days()->create([
                'rehearsal_date' => $day['date'],
                'starts_at' => $day['starts_at'],
                'ends_at' => $day['ends_at'],
                'notes' => $day['notes'],
            ]);
        }
    }

    private function singleDayTitle(Carbon $date): string
    {
        return __('app.rehearsal_title_single', [
            'date' => $date->translatedFormat('d.m.Y'),
        ]);
    }

    private function weekendTitle(Carbon $start, Carbon $end): string
    {
        $range = $start->translatedFormat('d.m.').' - '.$end->translatedFormat('d.m.Y');

        return __('app.rehearsal_title_weekend', ['range' => $range]);
    }
}
