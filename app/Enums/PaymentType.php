<?php

namespace App\Enums;

enum PaymentType: string
{
    case BANK_TRANSFER = 'bank_transfer';
    case CRYPTO = 'crypto';
}
