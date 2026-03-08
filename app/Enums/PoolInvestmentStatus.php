<?php

namespace App\Enums;

enum PoolInvestmentStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case ACTIVE = 'active';
    case REJECTED = 'rejected';
}
