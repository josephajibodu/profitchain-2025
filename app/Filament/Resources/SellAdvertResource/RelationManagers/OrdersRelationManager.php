<?php

namespace App\Filament\Resources\SellAdvertResource\RelationManagers;

use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reference')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reference')
            ->columns([
                Tables\Columns\TextColumn::make('reference'),
                Tables\Columns\TextColumn::make('buyer.username'),
                Tables\Columns\TextColumn::make('coin_amount')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => $state/100 . " Tiky")
                    ->description(fn(Order $record) => "at ₦$record->seller_unit_price / coin")
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_proof')
                    ->formatStateUsing(function ($state) {
                        if (! $state) return "-";

                        return new HtmlString("<a href='{$state}' target='_blank' class='underline'>View Proof</a>");
                    }),
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
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
