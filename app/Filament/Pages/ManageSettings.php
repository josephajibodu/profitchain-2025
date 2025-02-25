<?php

namespace App\Filament\Pages;

use App\Enums\Permissions;
use App\Enums\SystemPermissions;
use App\Settings\GeneralSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSetting::class;

    protected static ?int $navigationSort = 11;

    public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionTo(SystemPermissions::ManageSettings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('site_name')
                    ->label('Website Name')
                    ->inlineLabel()
                    ->required(),

                Forms\Components\TextInput::make('withdrawal_fee')
                    ->label('Withdrawal Fee in Percentage')
                    ->inlineLabel()
                    ->postfix("%")
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0.1)
                    ->maxValue(50)
                    ->required(),

                Forms\Components\TextInput::make('order_time_limit')
                    ->label('Order Time Limit (Minutes)')
                    ->inlineLabel()
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('total_coin')
                    ->label('Total Coins')
                    ->inlineLabel()
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('lowest_coin_price')
                    ->label('Lowest Coin Price')
                    ->inlineLabel()
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('highest_coin_price')
                    ->label('Highest Coin Price')
                    ->inlineLabel()
                    ->numeric()
                    ->required(),

                Forms\Components\FileUpload::make('price_list_pdf')
                    ->label('Price List PDF')
                    ->inlineLabel()
                    ->directory('price-lists')
                    ->openable()
                    ->acceptedFileTypes(['application/pdf'])
                    ->nullable(),

                Forms\Components\TextInput::make('donation_username')
                    ->label('Donation Username')
                    ->inlineLabel()
                    ->nullable(),

                Forms\Components\Repeater::make('community_links')
                    ->label('Community Links')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Platform Name')
                            ->required(),
                        Forms\Components\TextInput::make('label')
                            ->label('Label')
                            ->helperText('This will be the clickable text')
                            ->required(),
                        Forms\Components\TextInput::make('url')
                            ->label('URL')
                            ->url()
                            ->required(),
                    ])
                    ->collapsible()
                    ->columns(2)
            ])->columns(1)
            ->inlineLabel();
    }
}
