<?php

namespace App\Filament\Resources;

use App\Enums\AccountStatus;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Manage User';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('username')
                    ->disabled()
                    ->required(),
                Forms\Components\TextInput::make('first_name')
                    ->required(),
                Forms\Components\TextInput::make('last_name')
                    ->required(),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('whatsapp_number')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->disabled()
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('bank_name')
                    ->required(),
                Forms\Components\TextInput::make('bank_account_name')
                    ->required(),
                Forms\Components\TextInput::make('bank_account_number')
                    ->required(),
                Forms\Components\Select::make('account_status')
                    ->options(AccountStatus::class)
                    ->required(),
                Forms\Components\Textarea::make('ban_reason')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->description(fn(User $record) => "$record->full_name")
                    ->searchable(['username', 'first_name', 'last_name']),
                Tables\Columns\TextColumn::make('phone_number')
                    ->description(fn(User $record) => "$record->email")
                    ->searchable(['phone_number', 'email']),
                Tables\Columns\TextColumn::make('balance')
                    ->formatStateUsing(fn(User $record) => "Main: $record->balance")
                    ->description(fn(User $record) => "Reserver: $record->reserve_balance | Trade: $record->trading_balance")
                    ->searchable(['username', 'first_name', 'last_name']),
                Tables\Columns\TextColumn::make('account_status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('roles')
                    ->label('Roles')
                    ->formatStateUsing(function (User $record) {
                        return $record->roles->pluck('name')->join(' | ');
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

                Tables\Actions\Action::make('ban_user')
                    ->button()
                    ->visible(fn(User $record) => $record->account_status === AccountStatus::Active)
                    ->color('danger')
                    ->action(function (User $record) {
                        $record->account_status = AccountStatus::Banned;
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('User banned')
                            ->send();
                    }),

                Tables\Actions\Action::make('free_user')
                    ->button()
                    ->hidden(fn(User $record) => $record->account_status === AccountStatus::Active)
                    ->color('success')
                    ->action(function (User $record) {
                        $record->account_status = AccountStatus::Active;
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('User account activated')
                            ->send();
                    })
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
