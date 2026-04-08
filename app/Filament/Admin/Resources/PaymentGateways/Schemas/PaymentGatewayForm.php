<?php

namespace App\Filament\Admin\Resources\PaymentGateways\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentGatewayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Gateway Details')
                ->icon('heroicon-o-credit-card')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. USDT TRC20'),

                    TextInput::make('code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->placeholder('e.g. usdt_trc20')
                        ->helperText('Unique identifier used in code. Use lowercase with underscores.'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Configuration')
                ->icon('heroicon-o-cog-6-tooth')
                ->description('Add payment details such as wallet address, account number, bank name, etc.')
                ->schema([
                    KeyValue::make('config')
                        ->label('Config Fields')
                        ->keyLabel('Field')
                        ->valueLabel('Value')
                        ->addActionLabel('Add config field')
                        ->reorderable()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
