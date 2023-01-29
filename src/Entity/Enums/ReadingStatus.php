<?php

declare(strict_types=1);

namespace App\Entity\Enums;

enum ReadingStatus: string
{
    case SUCCESS = 'SUCCESS';
    case FAILED = 'FAILED';
    case NOT_FRESH = 'NOT FRESH';
}
