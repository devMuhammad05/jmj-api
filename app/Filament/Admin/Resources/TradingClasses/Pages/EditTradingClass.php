<?php

namespace App\Filament\Admin\Resources\TradingClasses\Pages;

use App\Filament\Admin\Resources\TradingClasses\TradingClassResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTradingClass extends EditRecord
{
    protected static string $resource = TradingClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
