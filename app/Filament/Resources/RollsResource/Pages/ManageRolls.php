<?php

namespace App\Filament\Resources\RollsResource\Pages;

use App\Filament\Resources\RollsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRolls extends ManageRecords
{
    protected static string $resource = RollsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
