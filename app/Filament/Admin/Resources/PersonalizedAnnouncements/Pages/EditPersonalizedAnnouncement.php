<?php

namespace App\Filament\Admin\Resources\PersonalizedAnnouncements\Pages;

use App\Filament\Admin\Resources\PersonalizedAnnouncements\PersonalizedAnnouncementResource;
use App\Models\PersonalizedAnnouncement;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPersonalizedAnnouncement extends EditRecord
{
    protected static string $resource = PersonalizedAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (): bool => $this->record instanceof PersonalizedAnnouncement && $this->record->isSent()),
        ];
    }

    protected function getFormActions(): array
    {
        if ($this->record instanceof PersonalizedAnnouncement && $this->record->isSent()) {
            return [];
        }

        return parent::getFormActions();
    }
}
