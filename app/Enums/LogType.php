<?php

namespace App\Enums;

enum LogType: string
{
    case MT = 'mt';
    case AUTH = 'auth';
    case KYC = 'kyc';
    case SIGNAL = 'signal';
    case USER = 'user';
    case SYSTEM = 'system';
    case SECURITY = 'security';

    public function label(): string
    {
        return match ($this) {
            self::MT => 'MetaTrader',
            self::AUTH => 'Authentication',
            self::KYC => 'KYC Verification',
            self::SIGNAL => 'Trading Signal',
            self::USER => 'User Management',
            self::SYSTEM => 'System',
            self::SECURITY => 'Security',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MT => 'warning',
            self::AUTH => 'info',
            self::KYC => 'primary',
            self::SIGNAL => 'success',
            self::USER => 'gray',
            self::SYSTEM => 'secondary',
            self::SECURITY => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::MT => 'heroicon-o-key',
            self::AUTH => 'heroicon-o-lock-closed',
            self::KYC => 'heroicon-o-identification',
            self::SIGNAL => 'heroicon-o-chart-bar',
            self::USER => 'heroicon-o-user',
            self::SYSTEM => 'heroicon-o-cog',
            self::SECURITY => 'heroicon-o-shield-exclamation',
        };
    }
}
