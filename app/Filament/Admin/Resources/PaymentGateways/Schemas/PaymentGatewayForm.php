<?php

namespace App\Filament\Admin\Resources\PaymentGateways\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
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

            Section::make('Wallet Details')
                ->icon('heroicon-o-wallet')
                ->description('Payment destination details shown to users during checkout.')
                ->schema([
                    TextInput::make('wallet_address')
                        ->label('Wallet Address')
                        ->maxLength(255)
                        ->placeholder('e.g. TQn9Y2khEsLJW1ChVWFMSMeRDow5KcbLSE')
                        ->columnSpanFull(),

                    Select::make('network')
                        ->label('Network')
                        ->options([
                            'TRC20' => 'TRC20 (Tron)',
                            'ERC20' => 'ERC20 (Ethereum)',
                            'BEP20' => 'BEP20 (BNB Smart Chain)',
                            'BTC' => 'BTC (Bitcoin)',
                            'SOL' => 'SOL (Solana)',
                            'other' => 'Other',
                        ])
                        ->native(false),

                    FileUpload::make('bar_code_path')
                        ->label('QR Code / Barcode Image')
                        ->image()
                        ->disk('public')
                        ->directory('gateways/barcodes')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
