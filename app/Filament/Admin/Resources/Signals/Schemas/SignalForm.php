<?php

namespace App\Filament\Admin\Resources\Signals\Schemas;

use App\Enums\SignalAction;
use App\Enums\SignalStatus;
use App\Enums\SignalType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SignalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── 1. Signal Identity ─────────────────────────────────────
                Section::make('Signal Identity')
                    ->description('Define the trading instrument and direction of this signal.')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Select::make('symbol')
                            ->label('Symbol')
                            ->options([
                                'Forex Majors' => [
                                    'EURUSD' => 'EUR/USD',
                                    'GBPUSD' => 'GBP/USD',
                                    'USDJPY' => 'USD/JPY',
                                    'AUDUSD' => 'AUD/USD',
                                    'USDCAD' => 'USD/CAD',
                                    'USDCHF' => 'USD/CHF',
                                    'NZDUSD' => 'NZD/USD',
                                ],
                                'Forex Crosses' => [
                                    'EURGBP' => 'EUR/GBP',
                                    'EURJPY' => 'EUR/JPY',
                                    'GBPJPY' => 'GBP/JPY',
                                ],
                                'Commodities' => [
                                    'XAUUSD' => 'XAU/USD — Gold',
                                    'XAGUSD' => 'XAG/USD — Silver',
                                ],
                                'Crypto' => [
                                    'BTCUSD' => 'BTC/USD — Bitcoin',
                                    'ETHUSD' => 'ETH/USD — Ethereum',
                                ],
                            ])
                            ->searchable()
                            ->required()
                            ->columnSpan(1),

                        Select::make('action')
                            ->label('Action')
                            ->options(SignalAction::class)
                            ->required()
                            ->columnSpan(1),

                        Select::make('type')
                            ->label('Signal Type')
                            ->options(SignalType::class)
                            ->default(SignalType::FREE)
                            ->required()
                            ->columnSpan(1),

                        Select::make('status')
                            ->label('Status')
                            ->options(SignalStatus::class)
                            ->default(SignalStatus::ACTIVE)
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // ── 2. Price Levels ────────────────────────────────────────
                Section::make('Price Levels')
                    ->description('Set the entry, stop loss, and take profit targets for this trade.')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        TextInput::make('entry_price')
                            ->label('Entry Price')
                            ->numeric()
                            ->step(0.00001)
                            ->required()
                            ->prefix('$')
                            ->helperText('The price at which to enter the trade')
                            ->columnSpan(1),

                        TextInput::make('stop_loss')
                            ->label('Stop Loss')
                            ->numeric()
                            ->step(0.00001)
                            ->required()
                            ->prefix('$')
                            ->helperText('Exit price if the trade moves against you')
                            ->columnSpan(1),

                        TextInput::make('take_profit_1')
                            ->label('Take Profit 1')
                            ->numeric()
                            ->step(0.00001)
                            ->required()
                            ->prefix('$')
                            ->helperText('First target price')
                            ->columnSpan(1),

                        TextInput::make('take_profit_2')
                            ->label('Take Profit 2')
                            ->numeric()
                            ->step(0.00001)
                            ->prefix('$')
                            ->helperText('Second target price (optional)')
                            ->columnSpan(1),

                        TextInput::make('take_profit_3')
                            ->label('Take Profit 3')
                            ->numeric()
                            ->step(0.00001)
                            ->prefix('$')
                            ->helperText('Third target price (optional)')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // ── 3. Outcome & Visibility ────────────────────────────────
                Section::make('Outcome & Visibility')
                    ->description('Track the signal result and control whether it is visible to subscribers.')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        TextInput::make('pips_result')
                            ->label('Pips Result')
                            ->numeric()
                            ->step(0.01)
                            ->default(0)
                            ->suffix('pips')
                            ->helperText('Calculated automatically when the signal is closed')
                            ->columnSpan(1),

                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true)
                            ->helperText('Make this signal visible to subscribers')
                            ->inline(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // ── 4. Analyst Notes ───────────────────────────────────────
                Section::make('Analyst Notes')
                    ->description('Optional commentary or analysis to accompany this signal.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->placeholder('Add your trade rationale, key levels to watch, or any relevant market context…')
                            ->helperText('Visible to users alongside the signal')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

            ])
            ->columns(1);
    }
}