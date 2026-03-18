<?php

namespace App\Filament\Admin\Resources\Pools\Tables;

use App\Enums\PoolStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PoolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total Capital')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('investor_count')
                    ->label('Investors')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('minimum_investment')
                    ->label('Min. Investment')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn (PoolStatus $state): string => match ($state) {
                            PoolStatus::ACTIVE => 'success',
                            PoolStatus::CLOSED => 'danger',
                            PoolStatus::PAUSED => 'warning',
                        },
                    )
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
