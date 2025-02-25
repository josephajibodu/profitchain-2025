<?php

namespace App\Filament\Resources\KycResource\Pages;

use App\Enums\KYCStatus;
use App\Filament\Resources\KycResource;
use App\Models\KycVerification;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListKycs extends ListRecords
{
    protected static string $resource = KycResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'pending';
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'pending' => Tab::make()
                ->badge(KycVerification::query()->where('status', KYCStatus::Pending)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', KYCStatus::Pending)),
            'rejected' => Tab::make()
                ->badge(KycVerification::query()->where('status', KYCStatus::Rejected)->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', KYCStatus::Rejected)),
            'completed' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', KYCStatus::Completed)),
        ];
    }
}
