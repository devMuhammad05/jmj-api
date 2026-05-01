<?php

namespace App\Filament\Admin\Resources\PersonalizedAnnouncements;

use App\Filament\Admin\Resources\PersonalizedAnnouncements\Pages\CreatePersonalizedAnnouncement;
use App\Filament\Admin\Resources\PersonalizedAnnouncements\Pages\EditPersonalizedAnnouncement;
use App\Filament\Admin\Resources\PersonalizedAnnouncements\Pages\ListPersonalizedAnnouncements;
use App\Filament\Admin\Resources\PersonalizedAnnouncements\Schemas\PersonalizedAnnouncementForm;
use App\Filament\Admin\Resources\PersonalizedAnnouncements\Tables\PersonalizedAnnouncementsTable;
use App\Models\PersonalizedAnnouncement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PersonalizedAnnouncementResource extends Resource
{
    protected static ?string $model = PersonalizedAnnouncement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Personalized Announcements';

    protected static \UnitEnum|string|null $navigationGroup = 'Communications';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PersonalizedAnnouncementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PersonalizedAnnouncementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPersonalizedAnnouncements::route('/'),
            'create' => CreatePersonalizedAnnouncement::route('/create'),
            'edit' => EditPersonalizedAnnouncement::route('/{record}/edit'),
        ];
    }
}
