<?php

namespace App\Enums;

enum SignalStatus: string
{
    case ACTIVE = 'active';
    case HIT_TP = 'hit_tp';
    case HIT_SL = 'hit_sl';
    case CLOSED = 'closed';
    case CANCELLED = 'cancelled';
}
