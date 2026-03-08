<?php

namespace App\Filament\Admin\Resources\PoolInvestments\Tables;

use App\Enums\PoolInvestmentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PoolInvestmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pool.name')
                    ->label('Pool')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label('Investor Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contribution')
                    ->label('Contribution')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('share_percentage')
                    ->label('Share')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->sortable(),
                ImageColumn::make('payment_proof_path')
                    ->label('Proof')
                    ->disk('public') // Assuming public disk, adjust if needed
                    ->circular(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (PoolInvestmentStatus $state): string => match ($state) {
                        PoolInvestmentStatus::PENDING => 'warning',
                        PoolInvestmentStatus::VERIFIED => 'info',
                        PoolInvestmentStatus::ACTIVE => 'success',
                        PoolInvestmentStatus::REJECTED => 'danger',
                    })
                    ->searchable(),
                IconColumn::make('terms_accepted')
                    ->label('Terms')
                    ->boolean(),
                TextColumn::make('verified_at')
                    ->label('Verified At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Applied At')
                    ->dateTime()
                    ->sortable(),
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
