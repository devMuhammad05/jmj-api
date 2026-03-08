<?php

namespace App\Filament\Admin\Resources\ProfitDistributions\Tables;

use App\Enums\ProfitDistributionStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProfitDistributionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('poolInvestment.full_name')
                    ->label('Investor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('distribution_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('profit_amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('pool_return')
                    ->label('Return')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (ProfitDistributionStatus $state): string => match ($state) {
                        ProfitDistributionStatus::PENDING => 'warning',
                        ProfitDistributionStatus::PROCESSED => 'success',
                        ProfitDistributionStatus::FAILED => 'danger',
                    })
                    ->searchable(),
                TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
