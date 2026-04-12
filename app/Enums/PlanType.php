<?php

namespace App\Enums;

enum PlanType: string
{
    case Signals = 'signals';
    case TradingClasses = 'trading_classes';

    public function label(): string
    {
        return match ($this) {
            self::Signals => 'Signals',
            self::TradingClasses => 'Trading Classes',
        };
    }
}
