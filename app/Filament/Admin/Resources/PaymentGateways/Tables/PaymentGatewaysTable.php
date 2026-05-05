<?php

namespace App\Filament\Admin\Resources\PaymentGateways\Tables;

use App\Enums\GatewayType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentGatewaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('bar_code_path')
                    ->label('QR')
                    ->getStateUsing(fn ($record) => $record->bar_code_path ? asset($record->bar_code_path) : null)
                    ->size(56)
                    ->square(),

                TextColumn::make('name')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('payment_type')
                    ->label('Type')
                    ->formatStateUsing(fn (GatewayType $state): string => format_status_text($state->value))
                    ->badge()
                    ->color(fn (GatewayType $state): string => match ($state) {
                        GatewayType::BANK_TRANSFER => 'info',
                        GatewayType::CRYPTO => 'success',
                    }),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('payments_count')
                    ->label('Payments')
                    ->counts('payments')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
