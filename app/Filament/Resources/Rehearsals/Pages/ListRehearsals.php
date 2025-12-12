<?php

namespace App\Filament\Resources\Rehearsals\Pages;

use App\Filament\Resources\Rehearsals\RehearsalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRehearsals extends ListRecords
{
    protected static string $resource = RehearsalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
