<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Rehearsals\RehearsalResource;
use App\Models\Rehearsal;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class MissingRehearsalDetails extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 20;

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('app.dashboard_missing_details');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Rehearsal::query()
                ->withCount([
                    'days as missing_times_count' => fn (Builder $query) => $query
                        ->whereNull('starts_at')
                        ->orWhere('starts_at', '')
                        ->orWhereNull('ends_at')
                        ->orWhere('ends_at', ''),
                ])
                ->where('end_date', '>=', today())
                ->where(function (Builder $query) {
                    $query
                        ->where('is_published', false)
                        ->orWhereNull('location_name')
                        ->orWhere('location_name', '')
                        ->orWhereNull('location_address')
                        ->orWhere('location_address', '')
                        ->orWhereNull('plan_path')
                        ->orWhere('plan_path', '')
                        ->orWhereHas('days', fn (Builder $dayQuery) => $dayQuery
                            ->whereNull('starts_at')
                            ->orWhere('starts_at', '')
                            ->orWhereNull('ends_at')
                            ->orWhere('ends_at', '')
                        );
                })
                ->orderBy('start_date'))
            ->columns([
                TextColumn::make('title')
                    ->label(__('app.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date_range')
                    ->label(__('app.date_range'))
                    ->state(fn (Rehearsal $record): string => $record->dateRangeLabel())
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('start_date', $direction)),
                IconColumn::make('is_published')
                    ->label(__('app.published'))
                    ->boolean()
                    ->alignCenter(),
                IconColumn::make('location_complete')
                    ->label(__('app.location'))
                    ->state(fn (Rehearsal $record): bool => filled($record->location_name) && filled($record->location_address))
                    ->trueIcon('heroicon-o-map-pin')
                    ->falseIcon('heroicon-o-exclamation-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning')
                    ->alignCenter(),
                IconColumn::make('plan_complete')
                    ->label(__('app.plan_available'))
                    ->state(fn (Rehearsal $record): bool => filled($record->plan_path))
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document')
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning')
                    ->alignCenter(),
                TextColumn::make('missing_times_count')
                    ->label(__('app.dashboard_missing_times_short'))
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'warning' : 'success')
                    ->alignCenter(),
            ])
            ->defaultSort('start_date')
            ->recordUrl(fn (Rehearsal $record): string => RehearsalResource::getUrl('edit', ['record' => $record]))
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
