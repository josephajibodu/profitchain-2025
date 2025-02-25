<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderDisputeResource\Pages;
use App\Filament\Resources\OrderDisputeResource\RelationManagers;
use App\Models\Order;
use App\Models\OrderDispute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class OrderDisputeResource extends Resource
{
    protected static ?string $model = OrderDispute::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationGroup = 'Main';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user.username')
                    ->relationship('user', 'username')
                    ->disabled(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('proofs')
                    ->openable()
                    ->deletable(false)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'close' => 'Close'
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.username')
                    ->description(fn(OrderDispute $dispute) => $dispute->user->full_name),
                Tables\Columns\TextColumn::make('proofs')
                    ->label('Proof')
                    ->formatStateUsing(function ($state) {
                        if (! $state) return "-";

                        $url = Storage::url($state);

                        return new HtmlString("<a href='$url' target='_blank'>View Proof</a>");
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListOrderDisputes::route('/'),
            'create' => Pages\CreateOrderDispute::route('/create'),
            // 'edit' => Pages\EditOrderDispute::route('/{record}/edit'),
        ];
    }
}
