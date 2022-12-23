<?php

declare(strict_types=1);

namespace App\Entity\Enums;

enum HttpMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
}
