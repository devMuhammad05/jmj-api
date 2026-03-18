<?php

namespace App\Filament\Admin\Resources\TradingClasses\Schemas;

use App\Enums\ClassPlatform;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TradingClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── 1. Class Details ───────────────────────────────────────
                Section::make('Class Details')
                    ->description(
                        'Define the title and description for the trading session.',
                    )
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        TextInput::make('title')
                            ->label('Class Title')
                            ->placeholder('e.g., Advanced Trading Strategies')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder(
                                'Outline what students will learn in this session...',
                            )
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // ── 2. Scheduling & Access ──────────────────────────────────
                Section::make('Scheduling & Access')
                    ->description(
                        'Set the date, time, and meeting platform for this class.',
                    )
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        DateTimePicker::make('scheduled_at')
                            ->label('Scheduled Date & Time')
                            ->required()
                            ->native(false)
                            ->displayFormat('M j, Y h:i A')
                            ->columnSpan(1),

                        Select::make('platform')
                            ->label('Platform')
                            ->options(ClassPlatform::class)
                            ->default(ClassPlatform::ZOOM)
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('meeting_link')
                            ->label('Meeting Link')
                            ->placeholder('https://zoom.us/j/...')
                            ->url()
                            ->helperText(
                                'The link users will use to join or learn more about the class.',
                            )
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // ── 3. Publishing ──────────────────────────────────────────
                Section::make('Publishing')
                    ->description(
                        'Control the visibility of this class in the Learning Hub.',
                    )
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true)
                            ->helperText(
                                'If turned off, users will not see this class.',
                            )
                            ->inline(false),
                    ])
                    ->collapsible(),
            ])
            ->columns(1);
    }
}
