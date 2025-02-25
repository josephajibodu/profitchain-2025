<?php

namespace App\Enums;

use App\Traits\HasValues;

enum KYCDocumentType: string
{
    use HasValues;

    case NationalID = 'national_id';
    case Passport = 'passport';
    case DriversLicense = 'drivers_license';
    case VotersCard = 'voters_card';
    case StudentID = 'student_id_card';

    public function getLabel() : string{
        return match ($this) {
            self::NationalID => 'NIN Slip',
            self::Passport => 'Intl. Passport',
            self::DriversLicense => 'Driver\'s License',
            self::VotersCard => 'Voter\'s Card',
            self::StudentID => 'Student ID',
        };
    }
}