<?php

namespace App\Enums;

enum AnnouncementTarget: string
{
    case All = 'all';
    case Subscribers = 'subscribers';

    public function label(): string
    {
        return match ($this) {
            self::All => 'All Users',
            self::Subscribers => 'Active Subscribers',
        };
    }
}
