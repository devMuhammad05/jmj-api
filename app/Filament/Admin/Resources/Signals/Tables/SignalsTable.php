<?php

namespace App\Filament\Admin\Resources\Signals\Tables;

use App\Enums\SignalAction;
use App\Enums\SignalStatus;
use App\Enums\SignalType;
use App\Models\Signal;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SignalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('symbol')
                    ->label('Symbol')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('action')
                    ->badge()
                    ->color(fn (SignalAction $state): string => match ($state) {
                        SignalAction::BUY, SignalAction::BUY_LIMIT, SignalAction::BUY_STOP => 'success',
                        SignalAction::SELL, SignalAction::SELL_LIMIT, SignalAction::SELL_STOP => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (SignalType $state): string => match ($state) {
                        SignalType::FREE => 'info',
                        SignalType::PREMIUM => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('entry_price')
                    ->label('Entry')
                    ->numeric(decimalPlaces: 5)
                    ->sortable(),
                TextColumn::make('stop_loss')
                    ->label('SL')
                    ->numeric(decimalPlaces: 5)
                    ->toggleable(),
                TextColumn::make('take_profit_1')
                    ->label('TP1')
                    ->numeric(decimalPlaces: 5)
                    ->toggleable(),
                TextColumn::make('take_profit_2')
                    ->label('TP2')
                    ->numeric(decimalPlaces: 5)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('take_profit_3')
                    ->label('TP3')
                    ->numeric(decimalPlaces: 5)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (SignalStatus $state): string => match ($state) {
                        SignalStatus::ACTIVE => 'info',
                        SignalStatus::HIT_TP => 'success',
                        SignalStatus::HIT_SL => 'danger',
                        SignalStatus::CLOSED => 'gray',
                        SignalStatus::CANCELLED => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('pips_result')
                    ->label('Pips')
                    ->numeric(decimalPlaces: 2)
                    ->color(fn ($state): string => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(SignalStatus::class)
                    ->default(SignalStatus::ACTIVE->value),
                SelectFilter::make('action')
                    ->options(SignalAction::class),
                SelectFilter::make('type')
                    ->options(SignalType::class),
                SelectFilter::make('symbol')
                    ->options([
                        'EURUSD' => 'EURUSD',
                        'GBPUSD' => 'GBPUSD',
                        'USDJPY' => 'USDJPY',
                        'AUDUSD' => 'AUDUSD',
                        'USDCAD' => 'USDCAD',
                        'XAUUSD' => 'XAUUSD (Gold)',
                        'BTCUSD' => 'BTCUSD (Bitcoin)',
                    ])
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('hit_tp')
                    ->label('TP')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Signal $record) => $record->status === SignalStatus::ACTIVE)
                    ->requiresConfirmation()
                    ->action(function (Signal $record) {
                        $pips = abs($record->take_profit_1 - $record->entry_price) * 10000;
                        if ($record->action === SignalAction::SELL || 
                            $record->action === SignalAction::SELL_LIMIT || 
                            $record->action === SignalAction::SELL_STOP) {
                            $pips = -$pips;
                        }
                        
                        $record->update([
                            'status' => SignalStatus::HIT_TP,
                            'pips_result' => $pips,
                        ]);
                        
                        Notification::make()
                            ->title('Signal Hit TP')
                            ->success()
                            ->send();
                    }),
                Action::make('hit_sl')
                    ->label('SL')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Signal $record) => $record->status === SignalStatus::ACTIVE)
                    ->requiresConfirmation()
                    ->action(function (Signal $record) {
                        $pips = abs($record->stop_loss - $record->entry_price) * 10000;
                        if ($record->action === SignalAction::BUY || 
                            $record->action === SignalAction::BUY_LIMIT || 
                            $record->action === SignalAction::BUY_STOP) {
                            $pips = -$pips;
                        }
                        
                        $record->update([
                            'status' => SignalStatus::HIT_SL,
                            'pips_result' => $pips,
                        ]);
                        
                        Notification::make()
                            ->title('Signal Hit SL')
                            ->danger()
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('warning')
                    ->visible(fn (Signal $record) => $record->status === SignalStatus::ACTIVE)
                    ->requiresConfirmation()
                    ->action(function (Signal $record) {
                        $record->update(['status' => SignalStatus::CANCELLED]);
                        Notification::make()
                            ->title('Signal Cancelled')
                            ->warning()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
