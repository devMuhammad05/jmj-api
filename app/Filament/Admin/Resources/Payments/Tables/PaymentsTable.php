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
                TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->sortable(),

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

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('status')
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
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
