<?php

namespace App\Filament\Admin\Resources\Subscriptions\Schemas;

use App\Models\Plan;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('Subscription Details')
                ->icon('heroicon-o-credit-card')
                ->schema([
                    Select::make('user_id')
                        ->label('User')
                        ->relationship('user', 'full_name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('plan_id')
                        ->label('Plan')
                        ->relationship('plan', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $plan = Plan::find($state);
                                if ($plan) {
                                    $starts = now();
                                    $set('starts_at', $starts->toDateTimeString());
                                    $set('ends_at', $starts->copy()->addDays($plan->duration_days)->toDateTimeString());
                                }
                            }
                        }),

                    DateTimePicker::make('starts_at')
                        ->label('Starts At')
                        ->required()
                        ->default(now()),

                    DateTimePicker::make('ends_at')
                        ->label('Ends At')
                        ->required(),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }
}
