<?php

declare(strict_types=1);

namespace App\Entity\Enums;

enum CheckingType: string
{
    case OVERALL = 'OVERALL';
    case ACTUALITY = 'ACTUALITY';
}
