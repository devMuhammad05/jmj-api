<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Enums\Role;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('country')
                    ->maxLength(255),
                DateTimePicker::make('email_verified_at')
                    ->label('Email Verified At')
                    ->helperText('Leave empty if email is not verified'),
                Select::make('role')
                    ->options(Role::class)
                    ->required()
                    ->default(Role::User)
                    ->helperText('Admin users can access the admin panel'),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->revealable()
                    ->maxLength(255)
                    ->helperText(fn (string $operation): string => $operation === 'edit'
                            ? 'Leave empty to keep current password'
                            : 'Minimum 8 characters recommended'
                    )
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
