<?php

namespace App\Filament\Resources\Rehearsals\Tables;

use App\Models\Rehearsal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RehearsalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('start_date')
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
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('days_count')
                    ->label(__('app.days'))
                    ->counts('days')
                    ->badge()
                    ->alignCenter(),
                IconColumn::make('plan_path')
                    ->label(__('app.plan_available'))
                    ->state(fn (Rehearsal $record): bool => filled($record->plan_path))
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document')
                    ->alignCenter(),
                IconColumn::make('is_published')
                    ->label(__('app.published'))
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label(__('app.published')),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
