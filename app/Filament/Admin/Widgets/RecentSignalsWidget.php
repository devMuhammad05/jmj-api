<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\SignalAction;
use App\Enums\SignalStatus;
use App\Models\Signal;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentSignalsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Trading Signals')
            ->query(
                Signal::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('symbol')
                    ->label('Symbol')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('action')
                    ->badge()
                    ->color(fn (SignalAction $state): string => match ($state) {
                        SignalAction::BUY, SignalAction::BUY_LIMIT, SignalAction::BUY_STOP => 'success',
                        SignalAction::SELL, SignalAction::SELL_LIMIT, SignalAction::SELL_STOP => 'danger',
                    }),
                TextColumn::make('entry_price')
                    ->label('Entry')
                    ->numeric(decimalPlaces: 5),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (SignalStatus $state): string => match ($state) {
                        SignalStatus::ACTIVE => 'info',
                        SignalStatus::TP => 'success',
                        SignalStatus::SL => 'danger',
                        SignalStatus::CLOSED => 'gray',
                        SignalStatus::CANCELLED => 'warning',
                    }),
                TextColumn::make('pips_result')
                    ->label('Pips')
                    ->numeric(decimalPlaces: 2)
                    ->color(fn ($state): string => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray')),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
