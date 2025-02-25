<?php

namespace App\Filament\Resources\SellAdvertResource\Pages;

use App\Filament\Resources\SellAdvertResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSellAdvert extends CreateRecord
{
    protected static string $resource = SellAdvertResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['minimum_sell'] *= 100;
        $data['max_sell'] *= 100;

        return $data;
    }
}
