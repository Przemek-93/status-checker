<?php

declare(strict_types=1);

namespace App\Entity\Enums;

enum HttpResponseStatus: int
{
    case CONTINUE_STATUS = 100;
    case PROCESSING = 102;

    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NO_CONTENT = 204;
    case PARTIAL_CONTENT = 206;

    case MULTIPLE_CHOICES = 300;

    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case NOT_ALLOWED = 405;
    case CONFLICT = 409;

    case SERVER_ERROR = 500;
    case BAD_GATEWAY = 502;
    case UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;

    public function isFailed(): bool
    {
        return $this === self::CONTINUE_STATUS ||
            ($this >= self::OK && $this < self::MULTIPLE_CHOICES);
    }
}
