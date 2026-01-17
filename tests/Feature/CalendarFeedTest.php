<?php

namespace Tests\Feature;

use App\Models\Rehearsal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CalendarFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_requires_valid_token(): void
    {
        config(['calendar.feed_token' => 'valid-token']);

        $this->get('/calendar/feed/wrong-token')->assertNotFound();
    }

    public function test_feed_renders_ics(): void
    {
        config(['calendar.feed_token' => 'valid-token']);

        $rehearsal = Rehearsal::create([
            'title' => 'Saturday rehearsal',
            'location_name' => 'Proberaum',
            'location_address' => 'Teststraße 1, 12345 Stadt',
            'notes' => 'Bringt die Streicherstellen mit.',
            'timezone' => 'Europe/Berlin',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today(),
            'plan_path' => null,
            'is_published' => true,
        ]);

        $rehearsal->days()->create([
            'rehearsal_date' => Carbon::today(),
            'starts_at' => '10:00',
            'ends_at' => '14:00',
            'notes' => 'Mit Dirigentin',
        ]);

        $response = $this->get('/calendar/feed/valid-token');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
        $response->assertSee('BEGIN:VEVENT');
        $response->assertSee('SUMMARY:Saturday rehearsal');
    }

    public function test_feed_includes_past_rehearsals(): void
    {
        config(['calendar.feed_token' => 'valid-token']);

        $rehearsal = Rehearsal::create([
            'title' => 'Past rehearsal',
            'location_name' => 'Proberaum',
            'location_address' => 'Teststraße 2, 12345 Stadt',
            'notes' => 'Vergangene Probe.',
            'timezone' => 'Europe/Berlin',
            'start_date' => Carbon::today()->subDays(7),
            'end_date' => Carbon::today()->subDays(7),
            'plan_path' => null,
            'is_published' => true,
        ]);

        $rehearsal->days()->create([
            'rehearsal_date' => Carbon::today()->subDays(7),
            'starts_at' => '19:00',
            'ends_at' => '21:00',
            'notes' => 'Mit Dirigentin',
        ]);

        $response = $this->get('/calendar/feed/valid-token');

        $response->assertOk();
        $response->assertSee('SUMMARY:Past rehearsal');
    }

    public function test_feed_excludes_unpublished_past_rehearsals(): void
    {
        config(['calendar.feed_token' => 'valid-token']);

        $rehearsal = Rehearsal::create([
            'title' => 'Private past rehearsal',
            'location_name' => 'Proberaum',
            'location_address' => 'Teststraße 3, 12345 Stadt',
            'notes' => 'Nicht im Feed.',
            'timezone' => 'Europe/Berlin',
            'start_date' => Carbon::today()->subDays(14),
            'end_date' => Carbon::today()->subDays(14),
            'plan_path' => null,
            'is_published' => false,
        ]);

        $rehearsal->days()->create([
            'rehearsal_date' => Carbon::today()->subDays(14),
            'starts_at' => '18:00',
            'ends_at' => '20:00',
            'notes' => null,
        ]);

        $response = $this->get('/calendar/feed/valid-token');

        $response->assertOk();
        $response->assertDontSee('SUMMARY:Private past rehearsal');
    }
}
