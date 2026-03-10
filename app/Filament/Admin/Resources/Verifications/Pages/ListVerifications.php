<?php

namespace App\Filament\Admin\Resources\Verifications\Pages;

use App\Filament\Admin\Resources\Verifications\VerificationResource;
use Filament\Resources\Pages\ListRecords;

class ListVerifications extends ListRecords
{
    protected static string $resource = VerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
