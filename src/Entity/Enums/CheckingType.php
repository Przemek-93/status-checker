<?php

declare(strict_types=1);

namespace App\Entity\Enums;

enum CheckingType: string
{
    case DATA_FRESHNESS = 'DATA_FRESHNESS';
    case HTTP_STATUS = 'HTTP_STATUS';
}
