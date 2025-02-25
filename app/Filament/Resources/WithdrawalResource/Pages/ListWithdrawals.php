<?php

namespace App\Filament\Resources\WithdrawalResource\Pages;

use App\Enums\WithdrawalStatus;
use App\Filament\Resources\WithdrawalResource;
use App\Models\Withdrawal;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListWithdrawals extends ListRecords
{
    protected static string $resource = WithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'pending' => Tab::make()
                ->badge(Withdrawal::query()->where('status', WithdrawalStatus::PENDING)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', WithdrawalStatus::PENDING)),
            'completed' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', WithdrawalStatus::COMPLETED)),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'pending';
    }
}
