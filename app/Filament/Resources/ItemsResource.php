<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemsResource\Pages;
use App\Filament\Resources\ItemsResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('qty')->required(),
            Forms\Components\Repeater::make('item_parts')
                ->relationship('itemParts')
                ->schema([
                    Forms\Components\Select::make('description')
                        ->label('Type')
                        ->options([
                            'Fabric' => 'Fabric',
                            'Sewing' => 'Sewing',
                            'Print' => 'Print',
                            'Bag' => 'Bag',
                            'Others' => 'Others',
                        ])
                        ->nullable(),

                    Forms\Components\TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->reactive()
                        ->debounce(2000)
                        ->label('Unit Price'),
                ])
                ->reactive() // Make the repeater reactive
                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                    // Calculate total based on the quantity and the sum of prices
                    $totalPrice = collect($state)->sum(fn($item) => (float)($item['price'] ?? 0));

                    // Set the total amount and credit balance
                    $set('cost',$totalPrice);
                }),
            Forms\Components\TextInput::make('cost')
                ->required()
                ->numeric()
                ->reactive()
                ->label('Unit Cost'),
            Forms\Components\TextInput::make('comment'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('qty')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('comment')->sortable()->searchable()])
            ->filters([
                //
            ])
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageItems::route('/'),
        ];
    }
}
