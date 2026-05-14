<?php

namespace App\Filament\Admin\Resources\MetaTraderCredentials\Pages;

use App\Filament\Admin\Resources\MetaTraderCredentials\MetaTraderCredentialResource;
use App\Jobs\ConnectMetaTraderAccount;
use Filament\Resources\Pages\CreateRecord;

class CreateMetaTraderCredential extends CreateRecord
{
    protected static string $resource = MetaTraderCredentialResource::class;

    protected function afterCreate(): void
    {
        ConnectMetaTraderAccount::dispatch($this->record->user, $this->record);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
