<?php

namespace App\Filament\Admin\Resources\Plans\Schemas;

use App\Models\Signal;
use App\Models\TradingClass;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('Plan Details')
                ->icon('heroicon-o-rectangle-stack')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state)))
                        ->placeholder('e.g. Pro'),

                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->placeholder('e.g. pro'),

                    TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->placeholder('0.00'),

                    TextInput::make('duration_days')
                        ->label('Duration (days)')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->placeholder('30'),

                    TextInput::make('level')
                        ->label('Level')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->placeholder('1')
                        ->unique(table: 'plans', column: 'level', ignoreRecord: true)
                        ->helperText('Higher level = more access (e.g. 1 = Free, 2 = Pro, 3 = VIP)'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpan(1),

            Section::make('Features')
                ->icon('heroicon-o-sparkles')
                ->description('Assign signals and trading classes included in this plan.')
                ->schema([
                    Select::make('signals')
                        ->relationship('signals', 'symbol')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->label('Signals'),

                    Select::make('tradingClasses')
                        ->relationship('tradingClasses', 'title')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->label('Trading Classes'),
                ])
                ->columnSpan(1),
        ]);
    }
}
