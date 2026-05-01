<?php

namespace App\Filament\Admin\Resources\PersonalizedAnnouncements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PersonalizedAnnouncementForm
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
                            ->placeholder('e.g. Special offer for you')
                            ->columnSpanFull(),

                        Textarea::make('message')
                            ->required()
                            ->rows(5)
                            ->placeholder('Write your message here…')
                            ->columnSpanFull(),
                    ])
                    ->disabled(fn ($record) => $record !== null && $record->isSent())
                    ->collapsible(),

                Section::make('Recipients')
                    ->description('Select the specific users who will receive this announcement.')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Select::make('users')
                            ->label('Select Users')
                            ->relationship('users', 'full_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->full_name} ({$record->email})")
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->disabled(fn ($record) => $record !== null && $record->isSent())
                    ->collapsible(),
            ])
            ->columns(1);
    }
}
