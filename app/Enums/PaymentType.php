<?php

namespace App\Enums;

enum PaymentType: string
{
    case PoolInvestment = 'pool_investment';
    case MetaCredential = 'meta_credential';
    case ClassSubscription = 'class_subscription';
    case Signals = 'signals';
}
