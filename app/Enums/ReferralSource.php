<?php

namespace App\Enums;

enum ReferralSource: string
{
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case Twitter = 'twitter';
    case TikTok = 'tiktok';
    case YouTube = 'youtube';
    case WhatsApp = 'whatsapp';
    case Telegram = 'telegram';
    case LinkedIn = 'linkedin';
    case Google = 'google';
    case Friend = 'friend';
    case Other = 'other';
}
