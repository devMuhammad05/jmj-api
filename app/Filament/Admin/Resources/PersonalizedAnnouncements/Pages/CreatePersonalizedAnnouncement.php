<?php

namespace App\Filament\Admin\Resources\PersonalizedAnnouncements\Pages;

use App\Filament\Admin\Resources\PersonalizedAnnouncements\PersonalizedAnnouncementResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePersonalizedAnnouncement extends CreateRecord
{
    protected static string $resource = PersonalizedAnnouncementResource::class;
}
