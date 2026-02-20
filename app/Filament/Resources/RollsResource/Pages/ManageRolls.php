<?php

namespace App\Filament\Resources\RollsResource\Pages;

use App\Filament\Resources\RollsResource;
use App\Models\Colors;
use App\Models\Roll;
use App\Models\Supplier;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;

class ManageRolls extends ManageRecords
{
    protected static string $resource = RollsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('createBatchRolls')
                ->label('Create Batch Rolls')
                ->icon('heroicon-o-squares-2x2')
                ->form([
                    Forms\Components\TextInput::make('batch_code')
                        ->label('Batch Code')
                        ->required(),
                    Forms\Components\Select::make('supplier_id')
                        ->label('Supplier')
                        ->options(fn () => Supplier::query()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Repeater::make('rolls')
                        ->schema([
                            Forms\Components\TextInput::make('roll_id')->required(),
                            Forms\Components\TextInput::make('weight')->numeric()->required(),
                            Forms\Components\TextInput::make('yardage')->numeric()->required(),
                            Forms\Components\Select::make('color_id')
                                ->label('Color')
                                ->options(fn () => Colors::query()->pluck('color_code', 'id'))
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->columns(4)
                        ->defaultItems(1)
                        ->minItems(1)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    DB::transaction(function () use ($data): void {
                        foreach ($data['rolls'] as $rollData) {
                            Roll::create([
                                'roll_id' => $rollData['roll_id'],
                                'batch_code' => $data['batch_code'],
                                'weight' => $rollData['weight'],
                                'yardage' => $rollData['yardage'],
                                'supplier_id' => $data['supplier_id'],
                                'color_id' => $rollData['color_id'],
                            ]);
                        }
                    });
                })
                ->successNotificationTitle('Batch rolls created successfully.'),
            Actions\CreateAction::make(),
        ];
    }
}
