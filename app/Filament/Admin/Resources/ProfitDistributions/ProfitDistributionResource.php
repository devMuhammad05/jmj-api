<?php

namespace App\Filament\Admin\Resources\ProfitDistributions;

use App\Filament\Admin\Resources\ProfitDistributions\Pages\CreateProfitDistribution;
use App\Filament\Admin\Resources\ProfitDistributions\Pages\EditProfitDistribution;
use App\Filament\Admin\Resources\ProfitDistributions\Pages\ListProfitDistributions;
use App\Filament\Admin\Resources\ProfitDistributions\Schemas\ProfitDistributionForm;
use App\Filament\Admin\Resources\ProfitDistributions\Tables\ProfitDistributionsTable;
use App\Models\ProfitDistribution;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProfitDistributionResource extends Resource
{
    protected static ?string $model = ProfitDistribution::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Profit Distributions';

    protected static ?string $modelLabel = 'Profit Distribution';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return ProfitDistributionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfitDistributionsTable::configure($table);
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
            'index' => ListProfitDistributions::route('/'),
            'create' => CreateProfitDistribution::route('/create'),
            'edit' => EditProfitDistribution::route('/{record}/edit'),
        ];
    }
}
