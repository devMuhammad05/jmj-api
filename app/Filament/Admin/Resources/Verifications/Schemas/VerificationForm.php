<?php

namespace App\Filament\Admin\Resources\Verifications\Schemas;

use App\Enums\IdType;
use App\Enums\VerificationStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VerificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (string $operation) => $operation === 'edit')
                    ->columnSpanFull(),
                Select::make('id_type')
                    ->label('ID Type')
                    ->options(IdType::class)
                    ->required(),
                TextInput::make('id_number')
                    ->label('ID Number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('id_card_front_img_url')
                    ->label('ID Card Front Image URL')
                    ->url()
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('id_card_back_img_url')
                    ->label('ID Card Back Image URL')
                    ->url()
                    ->columnSpanFull(),
                TextInput::make('selfie_img_url')
                    ->label('Selfie Image URL')
                    ->url()
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(VerificationStatus::class)
                    ->default(VerificationStatus::PENDING)
                    ->required(),
                Textarea::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->rows(3)
                    ->visible(fn ($get) => $get('status') === VerificationStatus::REJECTED->value)
                    ->required(fn ($get) => $get('status') === VerificationStatus::REJECTED->value)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
