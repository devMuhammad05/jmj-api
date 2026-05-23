<?php

namespace App\Enums;

enum PayoutAccountType: string
{
    case Bank = 'bank';
    case Crypto = 'crypto';
}
