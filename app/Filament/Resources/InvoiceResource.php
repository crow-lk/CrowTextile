<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceItemRelationManagerResource\RelationManagers\InvoiceItemsRelationManager;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\RelationManagers;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Dompdf\Dompdf;
use App\Http\Controllers\InvoiceController;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Vehicle;
use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Invoicing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'name')
                    ->required()
                    ->reactive()
                    ->searchable()
                    ->columnSpan('full'),

                Forms\Components\Repeater::make('items')
                    ->relationship('invoiceItems') // Define the relationship
                    ->reactive()
                    ->schema([
                        Forms\Components\Select::make('item_id')
                            ->label('Item')
                            ->relationship('item', 'name')
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Fetch the item based on the selected item_id
                                $item = Item::find($state);
                                if ($item) {
                                    // Update the unit_cost field with the item's unit cost
                                    $set('unit_cost', $item->cost);
                                } else {
                                    // Reset unit_cost if no item is found
                                    $set('unit_cost', 0);
                                }
                            }),
                        Forms\Components\TextInput::make('unit_cost')
                            ->label('Unit Cost')
                            ->required()
                            ->debounce(2000)
                            ->reactive(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // Get the current unit_cost value
                                $unitCost = $get('unit_cost');

                                // Recalculate total_amount whenever quantity changes
                                $set('total_amount', $state * $unitCost);
                            }),
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->label('Amount')
                            ->default(0)
                            ->reactive(),
                    ])
                    ->reactive() // Make the repeater reactive
                    ->columnSpanFull()->collapsible(),


            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Invoice ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->customer->title . ' ' . $state; // Assuming customer relationship is loaded
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Total Amount')
                    ->sortable(), // Format as currency
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Partial Paid' => 'warning',
                        'Paid' => 'success',
                        'Unpaid' => 'danger',
                    })
                    ->sortable(), // Format as currency
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Created')
                    ->dateTime()
                    ->sortable(), // Concatenate item details
            ])
            ->actions([
                Tables\Actions\EditAction::make()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInvoices::route('/'),
        ];
    }
}
