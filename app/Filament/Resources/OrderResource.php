<?php

namespace App\Filament\Resources;

use App\Actions\AdminCancelBuyOrder;
use App\Actions\AdminCompleteBuyOrder;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Main';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Buy Order';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('seller_id')
                    ->relationship('seller', 'id'),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('seller_unit_price')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('payment_time_limit')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('payment_proof'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('buyer.username')
                    ->description(fn(Order $record) => "from {$record->sellAdvert->user->username}"),
                Tables\Columns\TextColumn::make('coin_amount')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => $state/100 . " Tiky")
                    ->description(fn(Order $record) => "at ₦$record->seller_unit_price / coin")
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_proof')
                    ->getStateUsing(function (Order $record) {
                        if (! $record->payment_proof) return "-";

                        $url = Storage::url($record->payment_proof);

                        return new HtmlString("<a href='$url' target='_blank'>View Proof</a>");
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
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('release_to_seller')
                        ->requiresConfirmation()
                        ->modalDescription("The coin will be released back to the sellers sell order.")
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->action(function (AdminCancelBuyOrder $cancelBuyOrder, Order $record) {
                            try {
                                $cancelBuyOrder($record);

                                Notification::make()
                                    ->color('success')
                                    ->success()
                                    ->title('Successful')
                                    ->body("The coin has been released back to the seller")
                                    ->send();
                            } catch (Exception $ex) {
                                report($ex);

                                Notification::make()
                                    ->color('danger')
                                    ->danger()
                                    ->title('Error')
                                    ->body($ex->getMessage())
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('release_to_buyer')
                        ->requiresConfirmation()
                        ->modalDescription("The coin will be moved to the buyers reserve balance as normal.")
                        ->color('primary')
                        ->icon('heroicon-o-check')
                        ->action(function (AdminCompleteBuyOrder $completeBuyOrder, Order $record) {
                            try {
                                $completeBuyOrder($record);

                                Notification::make()
                                    ->color('success')
                                    ->success()
                                    ->title('Successful')
                                    ->body("The coin has been released back to the buyer")
                                    ->send();
                            } catch (Exception $ex) {
                                report($ex);

                                Notification::make()
                                    ->color('danger')
                                    ->danger()
                                    ->title('Error')
                                    ->body($ex->getMessage())
                                    ->send();
                            }
                        })
                ])->button()
                ->visible(fn(Order $record) => ! $record->inCompletedState())
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                     // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            // 'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
