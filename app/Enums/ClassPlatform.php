<?php

namespace App\Enums;

enum ClassPlatform: string
{
    case ZOOM = 'zoom';
    case TELEGRAM = 'telegram';
    case GOOGLE_MEET = 'google_meet';
    case YOUTUBE = 'youtube';
}
