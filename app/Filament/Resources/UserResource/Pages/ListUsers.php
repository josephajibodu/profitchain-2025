<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\Permissions;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),


            Actions\Action::make('transfer_coin')
                ->modalSubmitActionLabel('Send Coin')
                ->modalDescription('To credit/debit a users wallet directly. Click on the user in the table.')
                ->form([
                    TextInput::make('username')
                        ->required(),
                    TextInput::make('amount')
                        ->minValue(1)
                        ->numeric()
                        ->step(0.01)
                        ->required(),
                    TextInput::make('reason')
                        ->required(),
                ])
                ->action(function (array $data) {
                    try {
                        abort_if(
                            ! auth()->user()->hasPermissionTo(Permissions::ManageFunds),
                            403,
                            "You are forbidden from taking this action"
                        );

                        $user = User::query()->where('username', $data['username'])->first();

                        if (! $user) {
                            throw new \Exception("User with the username ({$data['username']}) not found.");
                        }

                        $user->credit($data['amount'], $data['reason']);

                        Notification::make()
                            ->color('success')
                            ->success()
                            ->title("User $user->username ($user->email) account credited")
                            ->send();
                    } catch (\Exception $ex) {
                        Notification::make()
                            ->danger()
                            ->title($ex->getMessage())
                            ->send();
                    }
                }),
        ];
    }
}
