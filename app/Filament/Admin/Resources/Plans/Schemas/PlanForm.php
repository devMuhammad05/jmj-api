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
        return $schema
            ->components([
                // ── 1. Plan Details ────────────────────────────────────────
                Section::make('Plan Details')
                    ->description('Define the name, slug, and pricing for this plan.')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Pro')
                            ->columnSpan(1),

                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->columnSpan(1),

                        TextInput::make('duration_days')
                            ->label('Duration (days)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('30')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // ── 2. Type & Level ────────────────────────────────────────
                Section::make('Type & Level')
                    ->description('Assign the plan type and access level.')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Select::make('type')
                            ->label('Plan Type')
                            ->options(PlanType::class)
                            ->required()
                            ->live()
                            ->helperText('Signals plans gate signal access; Trading Classes plans gate class access.')
                            ->columnSpan(1),

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
                            ->helperText('Unique per type. 2 = PRO, 3 = VIP.')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // ── 3. Publishing ──────────────────────────────────────────
                Section::make('Publishing')
                    ->description('Control whether this plan is available to users.')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('When disabled, users cannot subscribe to this plan.')
                            ->inline(false),
                    ])
                    ->collapsible(),
            ])
            ->columns(1);
    }
}
