<?php

namespace App\Filament\Resources;

use App\Enums\SellAdvertStatus;
use App\Filament\Resources\SellAdvertResource\Pages;
use App\Filament\Resources\SellAdvertResource\RelationManagers;
use App\Models\SellAdvert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SellAdvertResource extends Resource
{
    protected static ?string $model = SellAdvert::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Main';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Sell Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Details')
                    ->schema([

                        Forms\Components\TextInput::make('available_balance')
                            ->required()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->disabled()
                            ->numeric(),
                        Forms\Components\TextInput::make('remaining_balance')
                            ->required()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->disabled()
                            ->numeric(),
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('minimum_sell')
                                ->required()
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->numeric()
                                ->prefix('₦'),
                            Forms\Components\TextInput::make('max_sell')
                                ->required()
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->numeric()
                                ->prefix('₦')   ,
                        ])->columns(2),
                        Forms\Components\Textarea::make('terms')
                            ->required()
                            ->columnSpanFull(),
                    ])->columnSpan(['md' => 8]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Order Status')
                            ->schema([
                                Forms\Components\ToggleButtons::make('status')
                                    ->options(SellAdvertStatus::class)
                                    ->grouped(),

                                Forms\Components\Toggle::make('is_published')
                                    ->required()
                                    ->onColor('success')
                                    ->helperText('When turned off, your order will not be seen by buyers.'),
                            ]),
                        Forms\Components\Section::make('Banking Details')
                            ->schema([
                                Forms\Components\TextInput::make('bank_name')
                                    ->required(),
                                Forms\Components\TextInput::make('bank_account_name')
                                    ->required(),
                                Forms\Components\TextInput::make('bank_account_number')
                                    ->required(),
                            ]),
                    ])->columnSpan(['md' => 4]),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('user.username')
                    ->label('Seller')
                    ->description(fn(SellAdvert $record) => $record->user->full_name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('available_balance')
                    ->label('Available Coin')
                    ->formatStateUsing(fn($state) => to_money($state, 100, true))
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_sell')
                    ->money("NGN")
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_sell')
                    ->money("NGN")
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
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
            RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSellAdverts::route('/'),
            'create' => Pages\CreateSellAdvert::route('/create'),
            'edit' => Pages\EditSellAdvert::route('/{record}/edit'),
        ];
    }


}
