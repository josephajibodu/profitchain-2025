<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaderboardResource\Pages;
use App\Models\Leaderboard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaderboardResource extends Resource
{
    protected static ?string $model = Leaderboard::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-down';

    protected static ?string $navigationGroup = 'Manage User';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Leaderboard::query()
                    ->join('users', 'users.id', '=', 'leaderboard.user_id')
                    ->orderBy('leaderboard.amount', 'desc')
                    ->orderBy('users.username', 'asc')
                    ->orderBy('users.first_name', 'asc')
                    ->select('leaderboard.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('')
                    ->grow(false)
                    ->width('50px')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('user.full_name')
                    ->description(fn(Leaderboard $record) => $record->user->username),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn($state) => (float)$state/100)
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->selectable(false)
            ->emptyStateIcon('heroicon-o-trash')
            ->emptyStateHeading('Leaderboard empty')
            ->emptyStateDescription('The leaderboard is updated monthly. Watch out for the updated leaderboard.')
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListLeaderboards::route('/'),
            'create' => Pages\CreateLeaderboard::route('/create'),
            // 'edit' => Pages\EditLeaderboard::route('/{record}/edit'),
        ];
    }
}
