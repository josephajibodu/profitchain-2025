<?php

namespace App\Filament\Resources\CryptoTradeResource\Pages;

use App\Filament\Resources\CryptoTradeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCryptoTrade extends EditRecord
{
    protected static string $resource = CryptoTradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
