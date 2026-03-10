<?php

namespace App\Filament\Admin\Resources\Signals;

use App\Filament\Admin\Resources\Signals\Pages\CreateSignal;
use App\Filament\Admin\Resources\Signals\Pages\EditSignal;
use App\Filament\Admin\Resources\Signals\Pages\ListSignals;
use App\Filament\Admin\Resources\Signals\Schemas\SignalForm;
use App\Filament\Admin\Resources\Signals\Tables\SignalsTable;
use App\Models\Signal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SignalResource extends Resource
{
    protected static ?string $model = Signal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $navigationLabel = "Trading Signals";

    protected static \UnitEnum|string|null $navigationGroup = "Learning Hub";

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = "symbol";

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()
            ::where("status", \App\Enums\SignalStatus::ACTIVE)
            ->count() ?:
            null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return "info";
    }

    public static function form(Schema $schema): Schema
    {
        return SignalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SignalsTable::configure($table);
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
            "index" => ListSignals::route("/"),
            "create" => CreateSignal::route("/create"),
            "edit" => EditSignal::route("/{record}/edit"),
        ];
    }
}
