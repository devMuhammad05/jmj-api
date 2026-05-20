<?php

namespace App\Filament\Admin\Resources\Pools\Tables;

use App\Enums\MetaTraderCredentialConnectionStatus;
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
            ->modifyQueryUsing(fn ($query) => $query->withCount([
                'poolInvestments as approved_investors_count' => fn ($q) => $q->where('status', 'verified'),
                'poolInvestments as pending_investors_count' => fn ($q) => $q->where('status', 'pending'),
            ]))
            ->columns([
                TextColumn::make('name')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total Capital')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('each_contribution_amount')
                    ->label('Contribution Amount')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('approved_investors_count')
                    ->label('Approved Investors')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('pending_investors_count')
                    ->label('Pending Investors')
                    ->badge()
                    ->color('warning')
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
                TextColumn::make('metaTraderCredential.mt_account_number')
                    ->label('MT5 Account')
                    ->placeholder('—')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('metaTraderCredential.mt_server')
                    ->label('MT5 Server')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('metaTraderCredential.status')
                    ->label('MT5 Connection Status')
                    ->badge()
                    ->placeholder('—')
                    ->color(
                        fn (?MetaTraderCredentialConnectionStatus $state): string => match ($state) {
                            MetaTraderCredentialConnectionStatus::Connected => 'success',
                            MetaTraderCredentialConnectionStatus::Pending => 'warning',
                            default => 'gray',
                        },
                    ),
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
