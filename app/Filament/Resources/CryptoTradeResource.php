<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CryptoTradeResource\Pages;
use App\Filament\Resources\CryptoTradeResource\RelationManagers;
use App\Models\CryptoTrade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CryptoTradeResource extends Resource
{
    protected static ?string $model = CryptoTrade::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';


    protected static ?string $navigationGroup = 'Main';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'id'),
                Forms\Components\TextInput::make('reference')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('amount_sent')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('fee')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('network')
                    ->required(),
                Forms\Components\TextInput::make('wallet_address')
                    ->required(),
                Forms\Components\TextInput::make('comment'),
                Forms\Components\Textarea::make('metadata')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_sent')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('network')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wallet_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('comment')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListCryptoTrades::route('/'),
            'create' => Pages\CreateCryptoTrade::route('/create'),
            'edit' => Pages\EditCryptoTrade::route('/{record}/edit'),
        ];
    }
}
