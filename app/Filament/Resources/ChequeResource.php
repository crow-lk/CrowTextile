<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChequeResource\Pages;
use App\Models\Cheque;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ChequeResource extends Resource
{
    protected static ?string $model = Cheque::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Payments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Cheque Details')
                    ->schema([
                        Forms\Components\Select::make('direction')
                            ->label('Cheque Type')
                            ->options([
                                'from_account' => 'From Account (Payments Made)',
                                'to_account' => 'To Accounts (Payments Received)',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('cheque_number')
                            ->label('Cheque Number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bank_name')
                            ->label('Bank')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('party_name')
                            ->label(fn (Get $get): string => $get('direction') === 'from_account' ? 'Payee' : 'Payer')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->step(0.01)
                            ->required(),
                        Forms\Components\DatePicker::make('cheque_date')
                            ->label('Cheque Date'),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Due Date'),
                        Forms\Components\DateTimePicker::make('remind_at')
                            ->label('Reminder Time')
                            ->helperText('SMS will be sent automatically at this time.'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'cleared' => 'Cleared',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('direction')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'from_account' ? 'primary' : 'success')
                    ->formatStateUsing(fn (string $state): string => $state === 'from_account'
                        ? 'From Account'
                        : 'To Accounts'),
                TextColumn::make('cheque_number')
                    ->label('Cheque #')
                    ->searchable(),
                TextColumn::make('party_name')
                    ->label('Party')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('LKR', true),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date(),
                TextColumn::make('remind_at')
                    ->label('Reminder')
                    ->dateTime('Y-m-d H:i'),
                TextColumn::make('reminder_sent_at')
                    ->label('Sent')
                    ->dateTime('Y-m-d H:i'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cleared' => 'success',
                        'cancelled' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCheques::route('/'),
        ];
    }
}
