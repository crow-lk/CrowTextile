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
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'name')
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->placeholder('Select a customer')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columns(1),

                Forms\Components\Section::make('Invoice Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('invoiceItems')
                            ->reactive()
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('item_id')
                                            ->label('Item')
                                            ->relationship('item', 'name')
                                            ->required()
                                            ->reactive()
                                            ->searchable()
                                            ->placeholder('Select an item')
                                            ->columnSpan(2)
                                            ->createOptionForm(function () {
                                                return [
                                                    Forms\Components\TextInput::make('name')
                                                        ->required()
                                                        ->label('Item Name')
                                                        ->columnSpanFull(),
                                                    
                                                    Forms\Components\TextInput::make('qty')
                                                        ->required()
                                                        ->numeric()
                                                        ->label('Available Quantity'),
                                                    
                                                    Forms\Components\Section::make('Cost Breakdown')
                                                        ->schema([
                                                            Forms\Components\Repeater::make('item_costs')
                                                                ->relationship('itemCosts')
                                                                ->schema([
                                                                    Forms\Components\Select::make('cost_id')
                                                                        ->label('Cost Type')
                                                                        ->relationship('cost', 'name')
                                                                        ->required()
                                                                        ->reactive()
                                                                        ->searchable()
                                                                        ->createOptionForm(function () {
                                                                            return [
                                                                                Forms\Components\TextInput::make('name')
                                                                                    ->label('Cost Type')
                                                                                    ->required(),
                                                                            ];
                                                                        })
                                                                        ->createOptionUsing(function (array $data) {
                                                                            $cost = \App\Models\Cost::create([
                                                                                'name' => $data['name'],
                                                                            ]);
                                                                            return $cost->id;
                                                                        })
                                                                        ->columnSpan(1),

                                                                    Forms\Components\TextInput::make('price')
                                                                        ->required()
                                                                        ->numeric()
                                                                        ->reactive()
                                                                        ->debounce(2000)
                                                                        ->prefix('LKR')
                                                                        ->label('Unit Price')
                                                                        ->columnSpan(1),
                                                                ])
                                                                ->columns(2)
                                                                ->reactive()
                                                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                                    $totalPrice = collect($state)->sum(fn($item) => (float)($item['price'] ?? 0));
                                                                    $set('cost', $totalPrice);
                                                                })
                                                                ->columnSpanFull(),
                                                        ])
                                                        ->collapsible()
                                                        ->columnSpanFull(),
                                                    
                                                    Forms\Components\TextInput::make('cost')
                                                        ->required()
                                                        ->numeric()
                                                        ->reactive()
                                                        ->prefix('LKR')
                                                        ->label('Unit Cost'),
                                                    
                                                    Forms\Components\TextInput::make('comment')
                                                        ->label('Notes')
                                                        ->columnSpanFull(),
                                                ];
                                            })
                                            ->createOptionUsing(function (array $data) {
                                                $item = Item::create([
                                                    'name' => $data['name'],
                                                    'qty' => $data['qty'],
                                                    'cost' => $data['cost'],
                                                    'comment' => $data['comment'] ?? null,
                                                ]);
                                                return $item->id;
                                            })
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $item = Item::find($state);
                                                if ($item) {
                                                    $set('unit_cost', $item->cost);
                                                } else {
                                                    $set('unit_cost', 0);
                                                }
                                            }),

                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Quantity')
                                            ->required()
                                            ->numeric()
                                            ->reactive()
                                            ->debounce(1000)
                                            ->default(1)
                                            ->minValue(1)
                                            ->columnSpan(1),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('unit_cost')
                                            ->label('Unit Cost')
                                            ->required()
                                            ->numeric()
                                            ->debounce(2000)
                                            ->reactive()
                                            ->prefix('LKR')
                                            //disabled
                                            ->disabled()
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('total_amount')
                                            ->numeric()
                                            ->label('Total Amount')
                                            ->default(0)
                                            ->reactive()
                                            ->prefix('LKR')
                                            ->disabled()
                                            ->dehydrated()
                                            ->columnSpan(1)
                                            ->afterStateHydrated(function (callable $set, callable $get, $state) {
                                                if (empty($state) || $state == 0) {
                                                    $quantity = (float) ($get('quantity') ?? 0);
                                                    $unitCost = (float) ($get('unit_cost') ?? 0);
                                                    $set('total_amount', $quantity * $unitCost);
                                                }
                                            }),

                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('calculate_total')
                                                ->label('Calculate Total')
                                                ->icon('heroicon-o-calculator')
                                                ->color('primary')
                                                ->action(function (callable $set, callable $get, $livewire) {
                                                    $quantity = (float) ($get('quantity') ?? 0);
                                                    $unitCost = (float) ($get('unit_cost') ?? 0);
                                                    $total = $quantity * $unitCost;
                                                    $set('total_amount', $total);
                                                    
                                                    // Force Livewire to update the UI
                                                    $livewire->dispatch('refresh-form');
                                                })
                                        ])
                                        ->columnSpan(1)
                                        ->alignEnd(),
                                    ]),

                                Forms\Components\Textarea::make('comment')
                                    ->label('Additional Notes')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->reactive()
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add Another Item')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['item_id'] ? Item::find($state['item_id'])?->name : 'New Item')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('comment')
                            ->label('Invoice Notes')
                            ->rows(3)
                            ->placeholder('Add any additional comments or notes about this invoice')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
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
                        return $record->customer->title . ' ' . $state;
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Total Amount')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Partial Paid' => 'warning',
                        'Paid' => 'success',
                        'Unpaid' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Invoice $record) => route('invoices.pdf', $record->id))
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInvoices::route('/'),
        ];
    }
}