<?php

namespace App\Filament\Resources\RoleResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('username')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->description(fn(User $record) => $record->email)
                    ->searchable(['first_name', 'last_name', 'email']),
                Tables\Columns\TextColumn::make('username'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->label('Add User')
                    ->icon('heroicon-o-plus')
                    ->modalHeading("Add User to the Role")
                    ->modalSubmitActionLabel('Add User')
                    ->attachAnother(false),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Remove')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
