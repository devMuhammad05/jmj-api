<?php

namespace App\Filament\Admin\Resources\Rates;

use App\Filament\Admin\Resources\Rates\Pages\CreateRate;
use App\Filament\Admin\Resources\Rates\Pages\EditRate;
use App\Filament\Admin\Resources\Rates\Pages\ListRates;
use App\Filament\Admin\Resources\Rates\Schemas\RateForm;
use App\Filament\Admin\Resources\Rates\Tables\RatesTable;
use App\Models\Rate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class RateResource extends Resource
{
    protected static ?string $model = Rate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';


    public static function form(Schema $schema): Schema
    {
        return RateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRates::route('/'),
            'create' => CreateRate::route('/create'),
            'edit' => EditRate::route('/{record}/edit'),
        ];
    }
}
