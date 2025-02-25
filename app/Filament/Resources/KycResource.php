<?php

namespace App\Filament\Resources;

use App\Enums\KYCStatus;
use App\Filament\Resources\KycResource\Pages;
use App\Filament\Resources\KycResource\RelationManagers;
use App\Models\KycVerification;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class KycResource extends Resource
{
    protected static ?string $model = KycVerification::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Manage User';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('document_type')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('comment'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('user.username')
                    ->description(fn(KycVerification $record) => $record->user->full_name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_type')
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_proof')
                    ->getStateUsing(function (KycVerification $record) {
                        if (! $record->document) return "-";

                        $url = Storage::url($record->document);

                        return new HtmlString("<a href='$url' target='_blank'>View Doc</a>");
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
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
                Tables\Actions\Action::make('update_kyc')
                    ->button()
                    ->form([
                        Forms\Components\TextInput::make('comment')
                            ->label('Reason')
                            ->helperText('Add this if you are rejecting the KYC'),

                        Forms\Components\ToggleButtons::make('status')
                            ->inline()
                            ->options(KYCStatus::class)
                    ])
                    ->action(function (KycVerification $record, array $data) {
                        $reason = match ($data['status']) {
                            KYCStatus::Rejected->value => $data['comment'],
                            default => ''
                        };

                        $record->update([
                            'comment' => $reason,
                            'status' => $data['status']
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Kyc updated')
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
            'index' => Pages\ListKycs::route('/'),
            'create' => Pages\CreateKyc::route('/create'),
            // 'edit' => Pages\EditKyc::route('/{record}/edit'),
        ];
    }
}
