<?php

namespace App\Filament\Admin\Resources\Verifications\Pages;

use App\Filament\Admin\Resources\Verifications\VerificationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVerification extends EditRecord
{
    protected static string $resource = VerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
