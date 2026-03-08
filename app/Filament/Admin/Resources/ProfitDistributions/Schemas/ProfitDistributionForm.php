<?php

namespace App\Filament\Admin\Resources\ProfitDistributions\Schemas;

use App\Enums\ProfitDistributionStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfitDistributionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Distribution Details')
                    ->description('Investment source and amount information.')
                    ->schema([
                        Select::make('pool_investment_id')
                            ->relationship('poolInvestment', 'full_name') // Using full_name for easier identification
                            ->label('Investment')
                            ->searchable()
                            ->required(),
                        DatePicker::make('distribution_date')
                            ->default(now())
                            ->required(),
                        TextInput::make('profit_amount')
                            ->label('Profit Amount ($)')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('pool_return')
                            ->label('Pool Return (%)')
                            ->required()
                            ->numeric()
                            ->prefix('%'),
                    ])->columns(2),

                Section::make('Status & Processing')
                    ->description('Tracking the status of this distribution.')
                    ->schema([
                        Select::make('status')
                            ->options(ProfitDistributionStatus::class)
                            ->default(ProfitDistributionStatus::PENDING)
                            ->required(),
                        DateTimePicker::make('processed_at')
                            ->disabled(),
                        Textarea::make('failure_reason')
                            ->placeholder('Reason if the distribution failed...')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
