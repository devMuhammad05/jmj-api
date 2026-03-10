<?php

namespace App\Filament\Admin\Resources\Pools\Schemas;

use App\Enums\PoolStatus;
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

                    TextInput::make('investor_count')
                        ->label('Active Investors')
                        ->required()
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->placeholder('0')
                        ->hint('Number of active participants'),
                ])
                ->columns(2)
                ->columnSpan(1),
        ]);
    }
}
