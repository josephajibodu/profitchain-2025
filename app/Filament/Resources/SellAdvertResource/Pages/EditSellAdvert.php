<?php

namespace App\Filament\Resources\SellAdvertResource\Pages;

use App\Filament\Resources\SellAdvertResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSellAdvert extends EditRecord
{
    protected static string $resource = SellAdvertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['minimum_sell'] *= 100;
        $data['max_sell'] *= 100;

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Sell order updated!';
    }
}
