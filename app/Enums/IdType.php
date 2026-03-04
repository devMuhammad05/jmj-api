<?php

namespace App\Enums;

enum IdType: string
{
    case NationalId = 'national_id';
    case Passport = 'passport';
    case DrivingLicense = 'driving_license';
    case VotersCard = 'voters_card';
}
