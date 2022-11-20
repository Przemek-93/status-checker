<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Checker;

use App\Entity\Notification;
use App\Entity\NotificationReading;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Checker
{
    public function __construct(
        protected HttpClientInterface $httpClient,
        protected ResponseToReadingTransformer $transformer
    ) {
    }

    public function check(Notification $notification): NotificationReading
    {
        return $this->transformer->transform(
            $this->httpClient->request(
                $notification->getHttpMethod(),
                $notification->getUrl()
            )
        );
    }
}
