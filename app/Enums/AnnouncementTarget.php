<?php

namespace App\Enums;

enum AnnouncementTarget: string
{
    case All = 'all';
    case Subscribers = 'subscribers';
    case Plan = 'plan';

    public function label(): string
    {
        return match ($this) {
            self::All => 'All Users',
            self::Subscribers => 'Active Subscribers',
            self::Plan => 'Specific Plan',
        };
    }
}
