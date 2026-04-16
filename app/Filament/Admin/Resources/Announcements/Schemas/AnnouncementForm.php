<?php

namespace App\Filament\Admin\Resources\Announcements\Schemas;

use App\Enums\AnnouncementTarget;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Announcement')
                    ->description('Compose the announcement title and body.')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Platform Maintenance Scheduled')
                            ->columnSpanFull(),

                        Textarea::make('message')
                            ->required()
                            ->rows(5)
                            ->placeholder('Write your announcement here…')
                            ->columnSpanFull(),
                    ])
                    ->disabled(fn ($record) => $record?->isSent())
                    ->collapsible(),

                Section::make('Audience')
                    ->description('Choose who will receive this announcement.')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Select::make('target_audience')
                            ->label('Target Audience')
                            ->options(AnnouncementTarget::class)
                            ->required()
                            ->live()
                            ->default(AnnouncementTarget::All),

                        Select::make('plan_id')
                            ->label('Plan')
                            ->relationship('plan', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(
                                fn (Get $get): bool => $get('target_audience') === AnnouncementTarget::Plan->value
                            ),
                    ])
                    ->columns(2)
                    ->disabled(fn ($record) => $record?->isSent())
                    ->collapsible(),
            ])
            ->columns(1);
    }
}
