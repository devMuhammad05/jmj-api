<?php

namespace App\Enums;

enum SignalAction: string
{
    case BUY = 'buy';
    case SELL = 'sell';
    case BUY_LIMIT = 'buy_limit';
    case SELL_LIMIT = 'sell_limit';
    case BUY_STOP = 'buy_stop';
    case SELL_STOP = 'sell_stop';
}
