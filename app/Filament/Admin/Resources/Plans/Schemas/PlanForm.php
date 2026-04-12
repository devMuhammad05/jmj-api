<?php

namespace App\Filament\Admin\Resources\Plans\Schemas;

use App\Enums\PlanType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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

                    Select::make('type')
                        ->label('Plan Type')
                        ->options(PlanType::class)
                        ->required()
                        ->live()
                        ->helperText('Signals plans gate signal access; Trading Classes plans gate class access.'),

                    TextInput::make('level')
                        ->label('Level')
                        ->required()
                        ->numeric()
                        ->minValue(2)
                        ->default(2)
                        ->placeholder('2')
                        ->unique(
                            table: 'plans',
                            column: 'level',
                            ignoreRecord: true,
                            modifyRuleUsing: fn ($rule, Get $get) => $rule->where('type', $get('type')),
                        )
                        ->helperText('Unique per type. 2 = PRO, 3 = VIP.'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpan(1),

        ]);
    }
}
