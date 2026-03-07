<?php

namespace App\Filament\Admin\Resources\MetaTraderCredentials\Pages;

use App\Filament\Admin\Resources\MetaTraderCredentials\MetaTraderCredentialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMetaTraderCredentials extends ListRecords
{
    protected static string $resource = MetaTraderCredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
