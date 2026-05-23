<?php

namespace App\Filament\Admin\Resources\AppSettings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AppSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('General')
                ->icon('heroicon-o-building-office')
                ->description('Basic application identity and contact information.')
                ->schema([
                    TextInput::make('app_name')
                        ->label('App Name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),

                    TextInput::make('support_email')
                        ->label('Support Email')
                        ->email()
                        ->maxLength(255)
                        ->columnSpan(1),

                    TextInput::make('support_phone')
                        ->label('Support Phone')
                        ->tel()
                        ->maxLength(50)
                        ->columnSpan(1),

                    TextInput::make('support_whatsapp')
                        ->label('WhatsApp Number')
                        ->tel()
                        ->placeholder('+2348012345678')
                        ->maxLength(50)
                        ->columnSpan(1),

                    Textarea::make('address')
                        ->label('Office Address')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Social & Links')
                ->icon('heroicon-o-link')
                ->description('External URLs shown to users.')
                ->schema([
                    TextInput::make('facebook_url')
                        ->label('Facebook URL')
                        ->url()
                        ->maxLength(500)
                        ->columnSpan(1),

                    TextInput::make('twitter_url')
                        ->label('Twitter / X URL')
                        ->url()
                        ->maxLength(500)
                        ->columnSpan(1),

                    TextInput::make('instagram_url')
                        ->label('Instagram URL')
                        ->url()
                        ->maxLength(500)
                        ->columnSpan(1),

                    TextInput::make('deriv_referral_url')
                        ->label('Deriv Referral URL')
                        ->url()
                        ->maxLength(500)
                        ->columnSpan(1),

                    TextInput::make('youtube_tutorials_url')
                        ->label('YouTube Tutorials URL')
                        ->url()
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
