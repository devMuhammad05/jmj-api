<?php

namespace App\Filament\Admin\Resources\Rates\Schemas;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;

class RateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextColumn::make('key')
                    ->label('Rate Key')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('value')
                    ->label('Value (₦)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->money('NGN'),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->color('gray'),
            ]);
    }
}
