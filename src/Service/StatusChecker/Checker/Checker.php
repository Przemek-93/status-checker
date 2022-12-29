<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Checker;

use App\Entity\Enums\HttpResponseStatus;
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

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * Avoid using static access to class '\App\Entity\Enums\HttpResponseStatus' in method 'check'
     */
    public function check(Notification $notification): NotificationReading
    {
        $response = $this->httpClient->request(
            $notification->getHttpMethod()->value,
            $notification->getUrl()
        );

        $content = $response->getContent(false);
        if (mb_strlen($content) >= 2000) {
            $content = mb_substr($content, 0, 2000) . '...';
        }

        return $this->transformer->transform(
            new Response(
                HttpResponseStatus::from($response->getStatusCode()),
                json_decode($content, true) ?? [trim($content)]
            )
        );
    }
}
