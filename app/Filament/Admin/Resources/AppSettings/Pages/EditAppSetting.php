<?php

namespace App\Filament\Admin\Resources\AppSettings\Pages;

use App\Filament\Admin\Resources\AppSettings\AppSettingResource;
use App\Models\AppSetting;
use Filament\Resources\Pages\EditRecord;

class EditAppSetting extends EditRecord
{
    protected static string $resource = AppSettingResource::class;

    protected static ?string $title = 'App Settings';

    public function mount(int|string|null $record = null): void
    {
        parent::mount(AppSetting::getSettings()->getKey());
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
