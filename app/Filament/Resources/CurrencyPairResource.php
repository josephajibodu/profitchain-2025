<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyPairResource\Pages;
use App\Filament\Resources\CurrencyPairResource\RelationManagers;
use App\Models\CurrencyPair;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CurrencyPairResource extends Resource
{
    protected static ?string $model = CurrencyPair::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationGroup = 'Main';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencyPairs::route('/'),
            'create' => Pages\CreateCurrencyPair::route('/create'),
            'edit' => Pages\EditCurrencyPair::route('/{record}/edit'),
        ];
    }
}
