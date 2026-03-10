<?php

namespace App\Filament\Admin\Resources\TradingClasses\Pages;

use App\Filament\Admin\Resources\TradingClasses\TradingClassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTradingClasses extends ListRecords
{
    protected static string $resource = TradingClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
