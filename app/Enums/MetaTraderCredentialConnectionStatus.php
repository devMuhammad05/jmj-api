<?php

namespace App\Enums;

enum MetaTraderCredentialConnectionStatus: string
{
    case Pending = 'pending';
    case Connected = 'connected';
}
