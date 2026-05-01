<?php

namespace App\Filament\Admin\Resources\PersonalizedAnnouncements\Pages;

use App\Filament\Admin\Resources\PersonalizedAnnouncements\PersonalizedAnnouncementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPersonalizedAnnouncements extends ListRecords
{
    protected static string $resource = PersonalizedAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
