<?php

namespace App\Enums;

enum SignalStatus: string
{
    case ACTIVE = 'active';
    case TP = 'tp';
    case SL = 'sl';
    case CLOSED = 'closed';
    case CANCELLED = 'cancelled';
}
