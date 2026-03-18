<?php

namespace App\Filament\Admin\Resources\MetaTraderCredentials\Schemas;

use App\Enums\MetaTraderPlatformType;
use App\Enums\RiskLevel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MetaTraderCredentialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (string $operation) => $operation === 'edit')
                    ->columnSpanFull(),
                TextInput::make('mt_account_number')
                    ->label('MT Account Number')
                    ->required()
                    ->maxLength(50),
                TextInput::make('mt_server')
                    ->label('MT Server')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('e.g., Exness-MT5Real'),
                Select::make('platform_type')
                    ->label('Platform Type')
                    ->options(MetaTraderPlatformType::class)
                    ->default(MetaTraderPlatformType::MT5)
                    ->required(),
                TextInput::make('mt_password')
                    ->label('MT Password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->helperText(fn (string $operation): string => $operation === 'edit'
                            ? 'Leave empty to keep current password'
                            : 'This will be encrypted and stored securely'
                    ),
                TextInput::make('initial_deposit')
                    ->label('Initial Deposit')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01),
                Select::make('risk_level')
                    ->label('Risk Level')
                    ->options(RiskLevel::class)
                    ->required()
                    ->helperText('Determines position sizing and risk management'),
            ])
            ->columns(2);
    }
}
