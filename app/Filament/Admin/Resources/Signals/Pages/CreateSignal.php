<?php

namespace App\Filament\Admin\Resources\Signals\Pages;

use App\Filament\Admin\Resources\Signals\SignalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSignal extends CreateRecord
{
    protected static string $resource = SignalResource::class;
}
