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

class UpcomingRehearsals extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 10;

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('app.dashboard_upcoming_rehearsals');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Rehearsal::query()
                ->withCount('days')
                ->where('end_date', '>=', today())
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
                TextColumn::make('location_name')
                    ->label(__('app.location'))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('days_count')
                    ->label(__('app.days'))
                    ->badge()
                    ->alignCenter(),
                IconColumn::make('is_published')
                    ->label(__('app.published'))
                    ->boolean()
                    ->alignCenter(),
            ])
            ->defaultSort('start_date')
            ->recordUrl(fn (Rehearsal $record): string => RehearsalResource::getUrl('edit', ['record' => $record]))
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
