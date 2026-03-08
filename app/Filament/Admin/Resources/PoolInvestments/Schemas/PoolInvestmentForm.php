<?php

namespace App\Filament\Admin\Resources\PoolInvestments\Schemas;

use App\Enums\PoolInvestmentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PoolInvestmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Investor Details')
                    ->description('Personal and account information of the investor.')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'id')
                            ->searchable()
                            ->required(),
                        Select::make('pool_id')
                            ->relationship('pool', 'name')
                            ->required(),
                        TextInput::make('full_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone_number')
                            ->tel()
                            ->required(),
                    ])->columns(2),

                Section::make('Financial Information')
                    ->description('Investment amount and share details.')
                    ->schema([
                        TextInput::make('contribution')
                            ->label('Contribution Amount ($)')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->live(onBlur: true),
                        TextInput::make('share_percentage')
                            ->label('Pool Share (%)')
                            ->required()
                            ->numeric()
                            ->prefix('%')
                            ->default(0.0)
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),

                Section::make('Payment & Verification')
                    ->description('Verification of funds and banking details.')
                    ->schema([
                        FileUpload::make('payment_proof_path')
                            ->label('Payment Proof')
                            ->image()
                            ->directory('pool-investments/proofs')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('bank_name')
                            ->required(),
                        TextInput::make('account_number')
                            ->required(),
                        TextInput::make('account_name')
                            ->required(),
                        Toggle::make('terms_accepted')
                            ->label('Terms and Conditions Accepted')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(3),

                Section::make('Admin Review')
                    ->description('Status and rejection reason.')
                    ->schema([
                        Select::make('status')
                            ->options(PoolInvestmentStatus::class)
                            ->default(PoolInvestmentStatus::PENDING)
                            ->required(),
                        DateTimePicker::make('verified_at')
                            ->disabled(),
                        Textarea::make('rejection_reason')
                            ->placeholder('Provide a reason if rejected...')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
