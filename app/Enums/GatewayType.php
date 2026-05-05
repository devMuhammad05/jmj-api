<?php

namespace App\Enums;

enum GatewayType: string
{
    case BANK_TRANSFER = 'bank_transfer';
    case CRYPTO = 'crypto';
}
