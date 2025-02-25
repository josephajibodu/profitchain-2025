<?php

namespace App\Filament\Resources\LeaderboardResource\Pages;

use App\Filament\Resources\LeaderboardResource;
use App\Models\Leaderboard;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListLeaderboards extends ListRecords
{
    protected static string $resource = LeaderboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),

            Actions\Action::make('reset_leaderboard')
                ->requiresConfirmation()
                ->action(function () {
                    Leaderboard::query()->update(['amount' => 0]);

                    Notification::make()
                        ->success()
                        ->color('success')
                        ->title('Leaderboard reset successfully.')
                        ->send();
                })
        ];
    }
}
