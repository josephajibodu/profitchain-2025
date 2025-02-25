<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\KYCDocumentType;
use App\Enums\KYCStatus;
use App\Enums\Permissions;
use App\Filament\Resources\UserResource;
use App\Models\Kyc;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

            Actions\Action::make('assign_roles')
                ->label('Assign Role')
                ->button()
                ->icon('heroicon-o-user-group')
                ->form([
                    Select::make('roles')
                        ->multiple()
                        ->label('Select Roles')
                        ->options(Role::all()->except(1)->pluck('name', 'id'))
                        ->required()
                        ->preload()
                ])
                ->action(function (User $record, array $data) {
                    try {
                        abort_if(
                            ! auth()->user()->hasPermissionTo(Permissions::ManageFunds),
                            403,
                            "You are forbidden from taking this action"
                        );

                        $roles = Role::query()->whereIn('id', $data['roles'])->get();
                        $record->syncRoles($roles);

                        Notification::make()
                            ->title('Roles assigned successfully.')
                            ->success()
                            ->send();
                    } catch (\Exception $ex) {
                        Notification::make()
                            ->danger()
                            ->title($ex->getMessage())
                            ->send();
                    }
                }),

            Actions\Action::make('fund_user')
                ->form([
                    TextInput::make('amount')
                        ->minValue(1)
                        ->required(),
                    TextInput::make('reason')
                        ->required(),
                    ToggleButtons::make('action')
                        ->options([
                            'credit' => 'Credit', 'debit' => 'Debit'
                        ])
                        ->colors([
                            'credit' => 'success', 'debit' => 'danger'
                        ])
                        ->inline()
                        ->required()
                ])
                ->action(function (User $record, array $data) {
                    try {
                        abort_if(
                            ! auth()->user()->hasPermissionTo(Permissions::ManageFunds),
                            403,
                            "You are forbidden from taking this action"
                        );

                        $notificationTitle = "User account credited";

                        switch ($data['action']) {
                            case 'credit':
                                $record->credit($data['amount'], $data['reason']);
                                break;
                            case 'debit':
                                $record->debit($data['amount'], $data['reason']);
                                $notificationTitle = "User account debited";
                                break;
                        }

                        Notification::make()
                            ->color($data['action'] === 'credit' ? 'success' : 'danger')
                            ->success()
                            ->title($notificationTitle)
                            ->send();
                    } catch (\Exception $ex) {
                        Notification::make()
                            ->danger()
                            ->title($ex->getMessage())
                            ->send();
                    }
                }),

            Actions\Action::make('create_kyc')
                ->label('Create KYC')
                ->visible(fn(User $record) => ! ($record->kyc))
                ->form([
                    Select::make('document_type')
                        ->options(KYCDocumentType::class)
                        ->required(),
                    FileUpload::make('document')
                        ->label('Upload A Means of Identification')
                        ->image()
                        ->directory('kyc')
                        ->maxSize(2048)
                        ->required()
                ])
                ->action(function (array $data, User $record) {

                    Kyc::query()->create([
                        ...$data,
                        'user_id' => $record->id,
                        'status' => KYCStatus::Completed
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Document submitted and user kyc completed')
                        ->send();
                })
        ];
    }

}
