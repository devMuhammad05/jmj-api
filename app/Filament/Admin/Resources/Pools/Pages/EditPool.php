<?php

namespace App\Filament\Admin\Resources\Pools\Pages;

use App\Filament\Admin\Resources\Pools\PoolResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPool extends EditRecord
{
    protected static string $resource = PoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
