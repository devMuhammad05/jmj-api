<?php

namespace App\Filament\Admin\Resources\Pools;

use App\Filament\Admin\Resources\Pools\Pages\CreatePool;
use App\Filament\Admin\Resources\Pools\Pages\EditPool;
use App\Filament\Admin\Resources\Pools\Pages\ListPools;
use App\Filament\Admin\Resources\Pools\Schemas\PoolForm;
use App\Filament\Admin\Resources\Pools\Tables\PoolsTable;
use App\Models\Pool;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PoolResource extends Resource
{
    protected static ?string $model = Pool::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static ?string $navigationLabel = 'Pools';

    protected static ?string $modelLabel = 'Pool';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PoolForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PoolsTable::configure($table);
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
            'index' => ListPools::route('/'),
            'create' => CreatePool::route('/create'),
            'edit' => EditPool::route('/{record}/edit'),
        ];
    }
}
