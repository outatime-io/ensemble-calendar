<?php

namespace App\Filament\Resources\Rehearsals\Pages;

use App\Filament\Resources\Rehearsals\RehearsalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRehearsal extends EditRecord
{
    protected static string $resource = RehearsalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
