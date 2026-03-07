<?php

namespace App\Filament\Admin\Resources\MetaTraderCredentials;

use App\Filament\Admin\Resources\MetaTraderCredentials\Pages\CreateMetaTraderCredential;
use App\Filament\Admin\Resources\MetaTraderCredentials\Pages\EditMetaTraderCredential;
use App\Filament\Admin\Resources\MetaTraderCredentials\Pages\ListMetaTraderCredentials;
use App\Filament\Admin\Resources\MetaTraderCredentials\Schemas\MetaTraderCredentialForm;
use App\Filament\Admin\Resources\MetaTraderCredentials\Tables\MetaTraderCredentialsTable;
use App\Models\MetaTraderCredential;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MetaTraderCredentialResource extends Resource
{
    protected static ?string $model = MetaTraderCredential::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'MT Accounts';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'mt_account_number';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return MetaTraderCredentialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MetaTraderCredentialsTable::configure($table);
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
            'index' => ListMetaTraderCredentials::route('/'),
            'create' => CreateMetaTraderCredential::route('/create'),
            'edit' => EditMetaTraderCredential::route('/{record}/edit'),
        ];
    }
}
