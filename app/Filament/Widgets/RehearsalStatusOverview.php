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
                    ->where('end_date', '>=', today())
                    ->count()
            )
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('success'),
            Stat::make(
                __('app.dashboard_draft_rehearsals'),
                Rehearsal::query()
                    ->where('is_published', false)
                    ->where('end_date', '>=', today())
                    ->count()
            )
                ->icon(Heroicon::OutlinedEyeSlash)
                ->color('warning'),
            Stat::make(
                __('app.dashboard_missing_location'),
                Rehearsal::query()
                    ->where('end_date', '>=', today())
                    ->where(function ($query) {
                        $query
                            ->whereNull('location_name')
                            ->orWhere('location_name', '')
                            ->orWhereNull('location_address')
                            ->orWhere('location_address', '');
                    })
                    ->count()
            )
                ->icon(Heroicon::OutlinedMapPin)
                ->color('danger'),
            Stat::make(
                __('app.dashboard_missing_plan'),
                Rehearsal::query()
                    ->where('end_date', '>=', today())
                    ->where(function ($query) {
                        $query
                            ->whereNull('plan_path')
                            ->orWhere('plan_path', '');
                    })
                    ->count()
            )
                ->icon(Heroicon::OutlinedDocument)
                ->color('warning'),
            Stat::make(
                __('app.dashboard_missing_times'),
                RehearsalDay::query()
                    ->whereHas('rehearsal', fn ($query) => $query->where('end_date', '>=', today()))
                    ->where(function ($query) {
                        $query
                            ->whereNull('starts_at')
                            ->orWhere('starts_at', '')
                            ->orWhereNull('ends_at')
                            ->orWhere('ends_at', '');
                    })
                    ->count()
            )
                ->icon(Heroicon::OutlinedClock)
                ->color('warning'),
        ];
    }
}
