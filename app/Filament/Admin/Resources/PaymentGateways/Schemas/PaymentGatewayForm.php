<?php

namespace App\Filament\Admin\Resources\PaymentGateways\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PaymentGatewayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Gateway Details')
                ->icon('heroicon-o-credit-card')
                ->description('Basic information to identify this payment gateway.')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. USDT TRC20, Access Bank Transfer')
                        ->columnSpan(1),

                    TextInput::make('code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->placeholder('e.g. usdt_trc20, access_bank')
                        ->helperText('Unique identifier used in code. Use lowercase with underscores.')
                        ->columnSpan(1),

                    Toggle::make('is_active')
                        ->label('Set as Active')
                        ->helperText('Inactive gateways will not be shown to users at checkout.')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Payment Type')
                ->icon('heroicon-o-arrows-right-left')
                ->description('Select the type of payment details you want to configure for this gateway. You can only set one type per gateway.')
                ->schema([
                    Radio::make('payment_type')
                        ->label('')
                        ->options([
                            'crypto' => 'Cryptocurrency Wallet',
                            'bank' => 'Bank Account',
                        ])
                        ->descriptions([
                            'crypto' => 'Provide a wallet address and optional QR code for crypto payments.',
                            'bank' => 'Provide bank account details for direct bank transfers.',
                        ])
                        ->inline(false)
                        ->required()
                        ->live()
                        ->columnSpanFull(),
                ])
                ->columns(1),

            Section::make('Wallet Details')
                ->icon('heroicon-o-wallet')
                ->description('Cryptocurrency destination details shown to users during checkout.')
                ->visible(fn ($get) => $get('payment_type') === 'crypto')
                ->schema([
                    TextInput::make('wallet_address')
                        ->label('Wallet Address')
                        ->maxLength(255)
                        ->placeholder('e.g. TQn9Y2khEsLJW1ChVWFMSMeRDow5KcbLSE')
                        ->required(fn ($get) => $get('payment_type') === 'crypto')
                        ->columnSpanFull(),

                    Select::make('network')
                        ->label('Blockchain Network')
                        ->options([
                            'TRC20' => 'TRC20 — Tron',
                            'ERC20' => 'ERC20 — Ethereum',
                            'BEP20' => 'BEP20 — BNB Smart Chain',
                            'BTC' => 'BTC — Bitcoin',
                            'SOL' => 'SOL — Solana',
                            'other' => 'Other',
                        ])
                        ->placeholder('Select a network')
                        ->required(fn ($get) => $get('payment_type') === 'crypto')
                        ->native(false)
                        ->columnSpan(1),

                    Placeholder::make('bar_code_preview')
                        ->label('Current QR Code')
                        ->content(fn ($record): HtmlString|string => $record?->bar_code_path
                            ? new HtmlString('<img src="'.asset($record->bar_code_path).'" class="max-h-40 object-contain rounded-lg border border-gray-200 dark:border-gray-700" />')
                            : 'No QR code uploaded yet.'
                        )
                        ->visible(fn (string $operation) => $operation === 'edit')
                        ->columnSpan(1),

                    FileUpload::make('bar_code_path')
                        ->label('QR Code Image')
                        ->helperText('Upload a QR code image that users can scan to send payment.')
                        ->image()
                        ->disk('public')
                        ->directory('img/payment-gateway')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Bank Account Details')
                ->icon('heroicon-o-building-library')
                ->description('Bank account information for direct bank transfers.')
                ->visible(fn ($get) => $get('payment_type') === 'bank')
                ->schema([
                    TextInput::make('bank_name')
                        ->label('Bank Name')
                        ->maxLength(255)
                        ->placeholder('e.g. Access Bank, Zenith Bank')
                        ->required(fn ($get) => $get('payment_type') === 'bank')
                        ->columnSpan(1),

                    TextInput::make('account_name')
                        ->label('Account Name')
                        ->maxLength(255)
                        ->placeholder('e.g. JMJ Investments Ltd')
                        ->required(fn ($get) => $get('payment_type') === 'bank')
                        ->columnSpan(1),

                    TextInput::make('account_number')
                        ->label('Account Number')
                        ->maxLength(255)
                        ->placeholder('e.g. 0123456789')
                        ->required(fn ($get) => $get('payment_type') === 'bank')
                        ->columnSpan(1),
                ])
                ->columns(2),
        ]);
    }
}
