<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Checker;

use App\Entity\NotificationReading;
use DateTime;

class ResponseToReadingTransformer
{
    public function transform(Response $response): NotificationReading
    {
        return new NotificationReading(
            $response->status,
            new DateTime(),
            $response->toArray()
        );
    }
}
