<?php

namespace App\Filament\Admin\Resources\Payments\Tables;

use App\Enums\PaymentStatus;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('reference')
                //     ->label('Reference')
                //     ->searchable()
                //     ->sortable(),

                TextColumn::make('user.full_name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('gateway.name')
                    ->label('Gateway')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => format_status_text($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pool_investment' => 'success',
                        'meta_trader_credential' => 'info',
                        'subscription' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('status')
                    ->formatStateUsing(fn (PaymentStatus $state): string => format_status_text($state->value))
                    ->badge()
                    ->color(fn (PaymentStatus $state): string => match ($state) {
                        PaymentStatus::Pending => 'gray',
                        PaymentStatus::Submitted => 'info',
                        PaymentStatus::UnderReview => 'warning',
                        PaymentStatus::Approved => 'success',
                        PaymentStatus::Rejected => 'danger',
                        PaymentStatus::Failed => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(PaymentStatus::class)
                    ->label('Status'),
                SelectFilter::make('type')
                    ->options([
                        'pool_investment' => 'Pool Investment',
                        'meta_trader_credential' => 'MetaTrader Credential',
                        'subscription' => 'Subscription',
                    ])
                    ->label('Type'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
