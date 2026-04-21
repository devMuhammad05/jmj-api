<?php

namespace App\Enums;

enum PaymentStatus: string
{
    // case Submitted = 'submitted';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Failed = 'failed';
}
