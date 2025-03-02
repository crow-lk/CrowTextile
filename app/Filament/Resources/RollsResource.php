<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RollsResource\Pages;
use App\Filament\Resources\RollsResource\RelationManagers;
use App\Models\Roll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RollsResource extends Resource
{
    protected static ?string $model = Roll::class;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $navigationGroup = 'Material Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('roll_id')->required(),
Forms\Components\TextInput::make('weight')->required(),
Forms\Components\TextInput::make('yardage')->required(),
Forms\Components\Select::make('color_id')->required()->relationship('colors', 'color_code')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('roll_id')->sortable()->searchable(),
Tables\Columns\TextColumn::make('weight')->sortable()->searchable(),
Tables\Columns\TextColumn::make('yardage')->sortable()->searchable(),
Tables\Columns\TextColumn::make('color_id')->sortable()->searchable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ManageRolls::route('/'),
        ];
    }
}
