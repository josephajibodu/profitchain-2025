<?php

namespace App\Filament\Resources\SellAdvertResource\Pages;

use App\Filament\Resources\SellAdvertResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSellAdverts extends ListRecords
{
    protected static string $resource = SellAdvertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
