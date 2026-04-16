<?php

namespace App\Filament\Admin\Resources\Announcements\Pages;

use App\Filament\Admin\Resources\Announcements\AnnouncementResource;
use App\Models\Announcement;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (): bool => $this->record instanceof Announcement && $this->record->isSent()),
        ];
    }

    protected function getFormActions(): array
    {
        if ($this->record instanceof Announcement && $this->record->isSent()) {
            return [];
        }

        return parent::getFormActions();
    }
}
