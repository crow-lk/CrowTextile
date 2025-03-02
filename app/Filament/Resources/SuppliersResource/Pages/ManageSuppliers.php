<?php

namespace App\Filament\Resources\SuppliersResource\Pages;

use App\Filament\Resources\SuppliersResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSuppliers extends ManageRecords
{
    protected static string $resource = SuppliersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
