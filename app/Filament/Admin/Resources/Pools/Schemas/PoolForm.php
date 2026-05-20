<?php

namespace App\Filament\Admin\Resources\Pools\Schemas;

use App\Enums\MetaTraderPlatformType;
use App\Enums\PoolStatus;
use App\Enums\RiskLevel;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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

                    TextInput::make('number_of_investors')
                        ->label('Number of Investors')
                        ->required()
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->placeholder('e.g. 10')
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state): void {
                            $total = (float) $get('total_amount');
                            $count = (int) $state;
                            $set('each_contribution_amount', $count > 0 ? number_format($total / $count, 2, '.', '') : null);
                        })
                        ->rules([
                            fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                $total = (float) $get('total_amount');
                                $count = (int) $value;
                                if ($count > 0 && fmod($total, $count) !== 0.0) {
                                    $fail("The contribution amount (\${$total} ÷ {$count}) must be a whole number with no decimals.");
                                }
                            },
                        ]),
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
                        ->hint('Cumulative capital in the pool')
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state): void {
                            $total = (float) $state;
                            $count = (int) $get('number_of_investors');
                            $set('each_contribution_amount', $count > 0 ? number_format($total / $count, 2, '.', '') : null);
                        })
                        ->rules([
                            fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                $total = (float) $value;
                                $count = (int) $get('number_of_investors');
                                if ($count > 0 && fmod($total, $count) !== 0.0) {
                                    $fail("The total amount (\${$total} ÷ {$count} investors) must divide evenly with no decimal remainder.");
                                }
                            },
                        ]),

                    TextInput::make('each_contribution_amount')
                        ->label('Each Contribution ($)')
                        ->prefix('$')
                        ->disabled()
                        ->placeholder('Auto-calculated')
                        ->hint('total_amount ÷ number_of_investors'),

                ])
                ->columns(1)
                ->columnSpan(1),

            Section::make('MetaTrader Trading Account')
                ->description('Configure the MetaTrader account used for trading this pool.')
                ->icon('heroicon-o-computer-desktop')
                ->schema([
                    Toggle::make('add_mt5_account')
                        ->label('Attach MT5 Account')
                        ->helperText('Enable to configure the MetaTrader account for this pool. The connection will be processed in the background.')
                        ->live()
                        ->dehydrated(false)
                        ->inline(false)
                        ->columnSpanFull(),

                    TextInput::make('mt_account_number')
                        ->label('Account Number')
                        ->maxLength(50)
                        ->required(fn (Get $get): bool => (bool) $get('add_mt5_account'))
                        ->visible(fn (Get $get): bool => (bool) $get('add_mt5_account')),

                    TextInput::make('mt_server')
                        ->label('Server')
                        ->placeholder('e.g., Exness-MT5Real')
                        ->maxLength(100)
                        ->required(fn (Get $get): bool => (bool) $get('add_mt5_account'))
                        ->visible(fn (Get $get): bool => (bool) $get('add_mt5_account')),

                    Select::make('platform_type')
                        ->label('Platform')
                        ->options(MetaTraderPlatformType::class)
                        ->default(MetaTraderPlatformType::MT5)
                        ->required(fn (Get $get): bool => (bool) $get('add_mt5_account'))
                        ->visible(fn (Get $get): bool => (bool) $get('add_mt5_account')),

                    Select::make('risk_level')
                        ->label('Risk Level')
                        ->options(RiskLevel::class)
                        ->required(fn (Get $get): bool => (bool) $get('add_mt5_account'))
                        ->visible(fn (Get $get): bool => (bool) $get('add_mt5_account')),

                    TextInput::make('mt_password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->required(fn (Get $get): bool => (bool) $get('add_mt5_account'))
                        ->visible(fn (Get $get): bool => (bool) $get('add_mt5_account')),
                ])
                ->columns(3)
                ->columnSpanFull(),
        ]);
    }
}
