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
        return $schema->components([
            Section::make("Pool Information")
                ->description("Basic details about the investment pool.")
                ->schema([
                    TextInput::make("name")->required()->maxLength(255),
                    Select::make("status")
                        ->options(PoolStatus::class)
                        ->default(PoolStatus::ACTIVE)
                        ->required(),
                    TextInput::make("minimum_investment")
                        ->label('Minimum Investment ($)')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->default(1000.0),
                ])
                ->columns(2),

            Section::make("Financial Performance")
                ->description("Real-time tracking of pool performance.")
                ->schema([
                    TextInput::make("total_amount")
                        ->label('Total Capital ($)')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->default(0.0),
                    TextInput::make("investor_count")
                        ->label("Active Investors")
                        ->required()
                        ->numeric()
                        ->default(0),
                ])
                ->columns(3),
        ]);
    }
}
