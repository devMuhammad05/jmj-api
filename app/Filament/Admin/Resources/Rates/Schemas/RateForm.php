<?php

namespace App\Filament\Admin\Resources\Rates\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Rate Key')
                    ->required()
                    ->maxLength(255)
                    ->unique('rates', 'key', ignoreRecord: true),

                TextInput::make('value')
                    ->label('Value (₦)')
                    ->numeric()
                    ->required()
                    ->step(0.01),
            ]);
    }
}
