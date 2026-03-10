<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ActivitySubjectType: string implements HasLabel
{
    case USER = 'App\Models\User';
    case VERIFICATION = 'App\Models\Verification';
    case SIGNAL = 'App\Models\Signal';
    case MT_CREDENTIAL = 'App\Models\MetaTraderCredential';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::USER => 'User',
            self::VERIFICATION => 'Verification',
            self::SIGNAL => 'Signal',
            self::MT_CREDENTIAL => 'MetaTrader Credential',
        };
    }
}
