<?php

namespace App\Filament\Resources\CostsResource\Pages;

use App\Filament\Resources\CostsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCosts extends ManageRecords
{
    protected static string $resource = CostsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
