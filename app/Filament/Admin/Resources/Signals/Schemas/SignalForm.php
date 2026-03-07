<?php

namespace App\Filament\Admin\Resources\Signals\Schemas;

use App\Enums\SignalAction;
use App\Enums\SignalStatus;
use App\Enums\SignalType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SignalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('symbol')
                    ->label('Symbol')
                    ->options([
                        'EURUSD' => 'EURUSD',
                        'GBPUSD' => 'GBPUSD',
                        'USDJPY' => 'USDJPY',
                        'AUDUSD' => 'AUDUSD',
                        'USDCAD' => 'USDCAD',
                        'USDCHF' => 'USDCHF',
                        'NZDUSD' => 'NZDUSD',
                        'EURGBP' => 'EURGBP',
                        'EURJPY' => 'EURJPY',
                        'GBPJPY' => 'GBPJPY',
                        'XAUUSD' => 'XAUUSD (Gold)',
                        'XAGUSD' => 'XAGUSD (Silver)',
                        'BTCUSD' => 'BTCUSD (Bitcoin)',
                        'ETHUSD' => 'ETHUSD (Ethereum)',
                    ])
                    ->searchable()
                    ->required(),
                Select::make('action')
                    ->label('Action')
                    ->options(SignalAction::class)
                    ->required(),
                Select::make('type')
                    ->label('Signal Type')
                    ->options(SignalType::class)
                    ->default(SignalType::FREE)
                    ->required(),
                TextInput::make('entry_price')
                    ->label('Entry Price')
                    ->numeric()
                    ->step(0.00001)
                    ->required()
                    ->helperText('The price at which to enter the trade'),
                TextInput::make('stop_loss')
                    ->label('Stop Loss')
                    ->numeric()
                    ->step(0.00001)
                    ->required()
                    ->helperText('The price at which to exit if the trade goes against you'),
                TextInput::make('take_profit_1')
                    ->label('Take Profit 1')
                    ->numeric()
                    ->step(0.00001)
                    ->required()
                    ->helperText('First target price'),
                TextInput::make('take_profit_2')
                    ->label('Take Profit 2')
                    ->numeric()
                    ->step(0.00001)
                    ->helperText('Second target price (optional)'),
                TextInput::make('take_profit_3')
                    ->label('Take Profit 3')
                    ->numeric()
                    ->step(0.00001)
                    ->helperText('Third target price (optional)'),
                Select::make('status')
                    ->label('Status')
                    ->options(SignalStatus::class)
                    ->default(SignalStatus::ACTIVE)
                    ->required(),
                TextInput::make('pips_result')
                    ->label('Pips Result')
                    ->numeric()
                    ->step(0.01)
                    ->default(0)
                    ->helperText('Calculated automatically when signal is closed'),
                Toggle::make('is_published')
                    ->label('Published')
                    ->default(true)
                    ->helperText('Whether this signal is visible to users'),
                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Additional information or analysis for this signal'),
            ])
            ->columns(2);
    }
}
