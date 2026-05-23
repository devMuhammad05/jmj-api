<?php

namespace App\Filament\Admin\Resources\AppSettings;

use App\Filament\Admin\Resources\AppSettings\Pages\EditAppSetting;
use App\Filament\Admin\Resources\AppSettings\Schemas\AppSettingForm;
use App\Models\AppSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AppSettingResource extends Resource
{
    protected static ?string $model = AppSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'App Settings';

    protected static ?string $modelLabel = 'App Setting';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AppSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => EditAppSetting::route('/'),
        ];
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl('index');
    }
}
