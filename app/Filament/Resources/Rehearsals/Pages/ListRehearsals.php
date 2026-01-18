<?php

namespace App\Filament\Resources\Rehearsals\Pages;

use App\Filament\Resources\Rehearsals\RehearsalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListRehearsals extends ListRecords
{
    protected static string $resource = RehearsalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('app.add_rehearsal')),
        ];
    }

    public function getSubheading(): ?string
    {
        return __('app.past_rehearsals_hidden_hint');
    }

    public function getTabs(): array
    {
        return [
            'upcoming' => Tab::make(__('app.upcoming'))
                ->modifyQueryUsing(fn ($query) => $query->where('end_date', '>=', today())),
            'all' => Tab::make(__('app.all')),
            'past' => Tab::make(__('app.past'))
                ->modifyQueryUsing(fn ($query) => $query->where('end_date', '<', today())),
        ];
    }
}
