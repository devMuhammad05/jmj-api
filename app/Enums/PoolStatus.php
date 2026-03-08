<?php

namespace App\Enums;

enum PoolStatus: string
{
    case ACTIVE = 'active';
    case CLOSED = 'closed';
    case PAUSED = 'paused';
}
