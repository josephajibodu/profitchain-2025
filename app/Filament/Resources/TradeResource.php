<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TradeResource\Pages;
use App\Filament\Resources\TradeResource\RelationManagers;
use App\Models\Trade;
use App\Models\Withdrawal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TradeResource extends Resource
{
    protected static ?string $model = Trade::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Main';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reference')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'id')
                    ->required(),
                Forms\Components\Select::make('currency_pair_id')
                    ->relationship('currencyPair', 'id'),
                Forms\Components\TextInput::make('currency_pair_name')
                    ->required(),
                Forms\Components\TextInput::make('capital')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('total_roi')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('margin_applied')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('payment_time_limit')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->label('Name')
                    ->formatStateUsing(fn (Trade $record) => "{$record->user->full_name}")
                    ->description(fn (Trade $record) => "{$record->user->email}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency_pair_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capital')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_roi')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('margin_applied')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_time_limit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
            'index' => Pages\ListTrades::route('/'),
            'create' => Pages\CreateTrade::route('/create'),
            'edit' => Pages\EditTrade::route('/{record}/edit'),
        ];
    }
}
