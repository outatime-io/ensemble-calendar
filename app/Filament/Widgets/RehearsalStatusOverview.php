<?php

namespace App\Filament\Widgets;

use App\Models\Rehearsal;
use App\Models\RehearsalDay;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RehearsalStatusOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    public function getHeading(): ?string
    {
        return __('app.dashboard_rehearsal_status');
    }

    protected function getStats(): array
    {
        return [
            Stat::make(
                __('app.dashboard_upcoming_published'),
                Rehearsal::query()
                    ->published()
                    ->upcoming()
                    ->count()
            )
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('success'),
            Stat::make(
                __('app.dashboard_draft_rehearsals'),
                Rehearsal::query()
                    ->drafts()
                    ->upcoming()
                    ->count()
            )
                ->icon(Heroicon::OutlinedEyeSlash)
                ->color('warning'),
            Stat::make(
                __('app.dashboard_missing_location'),
                Rehearsal::query()
                    ->upcoming()
                    ->missingLocation()
                    ->count()
            )
                ->icon(Heroicon::OutlinedMapPin)
                ->color('danger'),
            Stat::make(
                __('app.dashboard_missing_plan'),
                Rehearsal::query()
                    ->upcoming()
                    ->missingPlan()
                    ->count()
            )
                ->icon(Heroicon::OutlinedDocument)
                ->color('warning'),
            Stat::make(
                __('app.dashboard_missing_times'),
                RehearsalDay::query()
                    ->whereHas('rehearsal', fn ($query) => $query->upcoming())
                    ->missingTimes()
                    ->count()
            )
                ->icon(Heroicon::OutlinedClock)
                ->color('warning'),
        ];
    }
}
