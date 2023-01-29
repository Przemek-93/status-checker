<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Checker\Strategy;

use App\Entity\Enums\HttpResponseStatus;
use App\Entity\Enums\ReadingStatus;
use App\Service\StatusChecker\Checker\Result;
use DateTime;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ActualityChecking implements CheckingInterface
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * Avoid using static access to class HttpResponseStatus.
     */
    public function check(ResponseInterface $response): Result
    {
        $httpCode = HttpResponseStatus::from($response->getStatusCode());
        $jsonContent = $response->getContent(false);
        $arrayContent = json_decode($jsonContent, true);
        if (!$arrayContent || $httpCode->isFailed()) {
            return new Result(
                $httpCode,
                ReadingStatus::FAILED,
                $jsonContent
            );
        }

        return new Result(
            $httpCode,
            $this->checkActuality($arrayContent),
            $jsonContent
        );
    }

    protected function checkActuality(array $arrayContent): ReadingStatus
    {
        foreach ($arrayContent['sensors'] as $sensor) {
            $readDate = new DateTime($sensor['data'][0]['read_at']);
            if ($readDate < new DateTime('now -1 hour', $readDate->getTimezone())) {
                return ReadingStatus::NOT_FRESH;
            }
        }

        return ReadingStatus::SUCCESS;
    }
}
