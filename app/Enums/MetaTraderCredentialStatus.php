<?php

namespace App\Enums;

enum MetaTraderCredentialStatus: string
{
    case PendingPayment = 'pending_payment';
    case PaymentSubmitted = 'payment_submitted';
    case Active = 'active';
    case Suspended = 'suspended';
}
