<?php

namespace App\Filament\Resources\PartsResource\Pages;

use App\Filament\Resources\PartsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageParts extends ManageRecords
{
    protected static string $resource = PartsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
