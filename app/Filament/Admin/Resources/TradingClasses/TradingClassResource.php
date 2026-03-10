<?php

namespace App\Filament\Admin\Resources\TradingClasses;

use App\Filament\Admin\Resources\TradingClasses\Pages\CreateTradingClass;
use App\Filament\Admin\Resources\TradingClasses\Pages\EditTradingClass;
use App\Filament\Admin\Resources\TradingClasses\Pages\ListTradingClasses;
use App\Filament\Admin\Resources\TradingClasses\Schemas\TradingClassForm;
use App\Filament\Admin\Resources\TradingClasses\Tables\TradingClassesTable;
use App\Models\TradingClass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TradingClassResource extends Resource
{
    protected static ?string $model = TradingClass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static \UnitEnum|string|null $navigationGroup = "Learning Hub";

    protected static ?string $modelLabel = "Trading Class";

    protected static ?string $pluralModelLabel = "Trading Classes";

    public static function form(Schema $schema): Schema
    {
        return TradingClassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TradingClassesTable::configure($table);
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
            "index" => ListTradingClasses::route("/"),
            "create" => CreateTradingClass::route("/create"),
            "edit" => EditTradingClass::route("/{record}/edit"),
        ];
    }
}
