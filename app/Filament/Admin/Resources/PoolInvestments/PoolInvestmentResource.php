<?php

namespace App\Filament\Admin\Resources\PoolInvestments;

use App\Enums\PoolInvestmentStatus;
use App\Filament\Admin\Resources\PoolInvestments\Pages\CreatePoolInvestment;
use App\Filament\Admin\Resources\PoolInvestments\Pages\EditPoolInvestment;
use App\Filament\Admin\Resources\PoolInvestments\Pages\ListPoolInvestments;
use App\Filament\Admin\Resources\PoolInvestments\Schemas\PoolInvestmentForm;
use App\Filament\Admin\Resources\PoolInvestments\Tables\PoolInvestmentsTable;
use App\Models\PoolInvestment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PoolInvestmentResource extends Resource
{
    protected static ?string $model = PoolInvestment::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', PoolInvestmentStatus::PENDING)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = 'Pool investments';

    protected static ?string $modelLabel = 'Investment Application';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        return PoolInvestmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PoolInvestmentsTable::configure($table);
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
            'index' => ListPoolInvestments::route('/'),
            'create' => CreatePoolInvestment::route('/create'),
            'edit' => EditPoolInvestment::route('/{record}/edit'),
        ];
    }
}
