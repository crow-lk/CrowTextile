<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Notify.lk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Notify.lk SMS')
                    ->description('Configure Notify.lk SMS gateway credentials.')
                    ->schema([
                        Forms\Components\Toggle::make('notify_enabled')
                            ->label('Enable Notify.lk'),
                        Forms\Components\TextInput::make('notify_user_id')
                            ->label('User ID')
                            ->required(fn (Get $get): bool => (bool) $get('notify_enabled'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('notify_api_key')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get): bool => (bool) $get('notify_enabled'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('notify_sender_id')
                            ->label('Sender ID')
                            ->required(fn (Get $get): bool => (bool) $get('notify_enabled'))
                            ->maxLength(255),
                        Forms\Components\TagsInput::make('notify_phones')
                            ->label('Notification Phones')
                            ->helperText('Add multiple numbers. Press Enter or use comma/semicolon.')
                            ->required(fn (Get $get): bool => (bool) $get('notify_enabled'))
                            ->splitKeys([',', ';'])
                            ->dehydrateStateUsing(static function ($state): array {
                                if (is_string($state)) {
                                    $parts = preg_split('/[;,\\n\\r]+/', $state) ?: [];
                                    return array_values(array_filter(array_map('trim', $parts), static fn ($value) => $value !== ''));
                                }

                                if (is_array($state)) {
                                    return array_values(array_filter(array_map('trim', $state), static fn ($value) => $value !== ''));
                                }

                                return [];
                            })
                            ->afterStateHydrated(function ($state, callable $set, ?Setting $record): void {
                                if ((empty($state) || $state === []) && $record?->notify_phone) {
                                    $set('notify_phones', [$record->notify_phone]);
                                }
                            }),
                        Forms\Components\Toggle::make('notify_unicode')
                            ->label('Use Unicode Messages')
                            ->helperText('Enable if you send non-English characters.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('notify_enabled')
                    ->boolean()
                    ->label('Enabled'),
                Tables\Columns\TextColumn::make('notify_user_id')
                    ->label('User ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notify_sender_id')
                    ->label('Sender ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notify_phones')
                    ->label('Phones')
                    ->formatStateUsing(function ($state, Setting $record): string {
                        if (is_array($state) && count($state) > 0) {
                            return implode(', ', $state);
                        }

                        return (string) ($record->notify_phone ?? '');
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('Y-m-d H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return Setting::query()->count() === 0;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSettings::route('/'),
        ];
    }
}
