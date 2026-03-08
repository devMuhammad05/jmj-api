<?php

namespace App\Enums;

enum ProfitDistributionStatus: string
{
    case PENDING = 'pending';
    case PROCESSED = 'processed';
    case FAILED = 'failed';
}
