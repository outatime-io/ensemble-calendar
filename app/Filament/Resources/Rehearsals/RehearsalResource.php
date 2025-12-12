<?php

namespace App\Filament\Resources\Rehearsals;

use App\Filament\Resources\Rehearsals\Pages\CreateRehearsal;
use App\Filament\Resources\Rehearsals\Pages\EditRehearsal;
use App\Filament\Resources\Rehearsals\Pages\ListRehearsals;
use App\Filament\Resources\Rehearsals\Schemas\RehearsalForm;
use App\Filament\Resources\Rehearsals\Tables\RehearsalsTable;
use App\Models\Rehearsal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RehearsalResource extends Resource
{
    protected static ?string $model = Rehearsal::class;

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RehearsalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RehearsalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRehearsals::route('/'),
            'create' => CreateRehearsal::route('/create'),
            'edit' => EditRehearsal::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('app.rehearsals');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation_planning');
    }

    public static function getModelLabel(): string
    {
        return __('app.rehearsal');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.rehearsals');
    }

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $data = static::syncDateRange($data);
        $data['created_by'] = auth()?->id();
        $data['timezone'] = $data['timezone'] ?? config('app.timezone');

        return $data;
    }

    protected static function mutateFormDataBeforeSave(array $data): array
    {
        $data = static::syncDateRange($data);
        $data['timezone'] = $data['timezone'] ?? config('app.timezone');

        return $data;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    private static function syncDateRange(array $data): array
    {
        $days = collect($data['days'] ?? [])
            ->filter(fn (array $day) => !empty($day['rehearsal_date']))
            ->sortBy('rehearsal_date')
            ->values();

        if ($days->isNotEmpty()) {
            $data['start_date'] = $days->first()['rehearsal_date'];
            $data['end_date'] = $days->last()['rehearsal_date'];
        }

        return $data;
    }
}
