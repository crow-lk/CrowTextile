<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuppliersResource\Pages;
use App\Filament\Resources\SuppliersResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuppliersResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Material Management';

    public static function form(Form $form): Form
    {
        return $form->schema([Forms\Components\TextInput::make('name')->required(), Forms\Components\TextInput::make('contact_person')->required(), Forms\Components\TextInput::make('phone')->required(), Forms\Components\TextInput::make('email')->required(), Forms\Components\TextInput::make('Address')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([Tables\Columns\TextColumn::make('name')->sortable()->searchable(), Tables\Columns\TextColumn::make('contact_person')->sortable()->searchable(), Tables\Columns\TextColumn::make('phone')->sortable()->searchable(), Tables\Columns\TextColumn::make('email')->sortable()->searchable(), Tables\Columns\TextColumn::make('Address')->sortable()->searchable()])
            ->filters([
                //
            ])
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSuppliers::route('/'),
        ];
    }
}
