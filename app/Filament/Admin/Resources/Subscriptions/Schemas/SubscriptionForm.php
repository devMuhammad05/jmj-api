<?php

namespace App\Filament\Admin\Resources\Subscriptions\Schemas;

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
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
                        ->nullable(),

                    DateTimePicker::make('ends_at')
                        ->label('Ends At')
                        ->nullable(),

                    Placeholder::make('status_display')
                        ->label('Status')
                        ->content(fn ($record): string => $record?->status instanceof SubscriptionStatus ? $record->status->label() : 'N/A'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(false)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }
}
