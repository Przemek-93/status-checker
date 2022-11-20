<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Checker;

use App\Entity\NotificationReading;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ResponseToReadingTransformer
{
    public function transform(ResponseInterface $response): NotificationReading
    {
        return (new NotificationReading())
            ->setContent($response->toArray())
            ->setStatus($response->getStatusCode());
    }
}
