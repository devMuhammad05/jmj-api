<?php

namespace App\Filament\Admin\Resources\Pools\Schemas;

use App\Enums\MetaTraderPlatformType;
use App\Enums\PoolStatus;
use App\Enums\RiskLevel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('Pool Information')
                ->description('Basic details about the investment pool.')
                ->icon('heroicon-o-circle-stack')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. Growth Fund Alpha')
                        ->columnSpanFull(),

                    Select::make('status')
                        ->options(PoolStatus::class)
                        ->default(PoolStatus::ACTIVE)
                        ->required()
                        ->native(false),

                    TextInput::make('minimum_investment')
                        ->label('Minimum Investment ($)')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->default(1000.0)
                        ->minValue(0)
                        ->placeholder('1000.00'),
                ])
                ->columns(2)
                ->columnSpan(1),

            Section::make('Financial Performance')
                ->description('Real-time tracking of pool performance.')
                ->icon('heroicon-o-chart-bar')
                ->schema([
                    TextInput::make('total_amount')
                        ->label('Total Capital ($)')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->default(0.0)
                        ->minValue(0)
                        ->placeholder('0.00')
                        ->hint('Cumulative capital in the pool'),

                ])
                ->columns(1)
                ->columnSpan(1),

            Section::make('MetaTrader Trading Account')
                ->description('Configure the MetaTrader account used for trading this pool.')
                ->icon('heroicon-o-computer-desktop')
                ->schema([
                    TextInput::make('mt_account_number')
                        ->label('Account Number')
                        ->required()
                        ->maxLength(50),
                    TextInput::make('mt_server')
                        ->label('Server')
                        ->required()
                        ->placeholder('e.g., Exness-MT5Real')
                        ->maxLength(100),
                    Select::make('platform_type')
                        ->label('Platform')
                        ->required()
                        ->options(MetaTraderPlatformType::class)
                        ->default(MetaTraderPlatformType::MT5),
                    Select::make('risk_level')
                        ->label('Risk Level')
                        ->required()
                        ->options(RiskLevel::class),
                    TextInput::make('mt_password')
                        ->label('Password')
                        ->required()
                        ->password()
                        ->revealable(),
                ])
                ->columns(3)
                ->columnSpanFull(),
        ]);
    }
}
