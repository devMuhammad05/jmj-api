<?php

namespace App\Filament\Admin\Resources\MetaTraderCredentials\Pages;

use App\Filament\Admin\Resources\MetaTraderCredentials\MetaTraderCredentialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMetaTraderCredential extends EditRecord
{
    protected static string $resource = MetaTraderCredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
