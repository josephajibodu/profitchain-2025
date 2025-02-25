<?php

namespace App\Filament\Resources;

use App\Enums\WithdrawalStatus;
use App\Filament\Resources\WithdrawalResource\Pages;
use App\Models\Withdrawal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

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
                Forms\Components\TextInput::make('bank_name')
                    ->required(),
                Forms\Components\TextInput::make('bank_account_name')
                    ->required(),
                Forms\Components\TextInput::make('bank_account_number')
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
                Tables\Columns\TextColumn::make('reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->label('Name')
                    ->formatStateUsing(fn (Withdrawal $record) => "{$record->user->full_name}")
                    ->description(fn (Withdrawal $record) => "{$record->user->email}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => (float) $state / 100)
                    ->description(fn(Withdrawal $record) => "@ " . to_money((float)$record->rate))
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_payable')
                    ->label("Payable in ₦")
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => to_money((float)$state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->description(fn (Withdrawal $record) => $record->bank_account_number)
                    ->searchable(['bank_account_name', 'bank_account_number', 'bank_name'])
                    ->copyable()
                    ->copyMessage('Bank account number copied')
                    ->copyMessageDuration(1500)
                    ->copyableState(fn (Withdrawal $record) => $record->bank_account_number),
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

                Tables\Actions\Action::make('settle')
                    ->icon('heroicon-o-check')
                    ->button()
                    ->requiresConfirmation()
                    ->visible(fn(Withdrawal $record) => $record->isPending())
                    ->action(function (Withdrawal $record, Tables\Actions\Action $action) {
                        $record->update(['status' => WithdrawalStatus::COMPLETED]);

                        $action->success();
                    })
                    ->successNotificationTitle('Direct Sales settled successfully'),

                Tables\Actions\Action::make('return')
                    ->icon('heroicon-o-x-mark')
                    ->button()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(Withdrawal $record) => $record->isPending())
                    ->action(function (Withdrawal $record, Tables\Actions\Action $action) {
                        return DB::transaction(function () use ($action, $record) {
                            $user = $record->user;

                            $user->credit($record->amount / 100, "Refund Direct Sale $record->reference");

                            $record->update(['status' => WithdrawalStatus::CANCELLED]);

                            $action->success();
                        });
                    })
                    ->successNotificationTitle('Direct Sales cancelled and coin refunded successfully'),
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
            'index' => Pages\ListWithdrawals::route('/'),
            'create' => Pages\CreateWithdrawal::route('/create'),
            'edit' => Pages\EditWithdrawal::route('/{record}/edit'),
        ];
    }
}
