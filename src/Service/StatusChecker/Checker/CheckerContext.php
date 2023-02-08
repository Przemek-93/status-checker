<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Checker;

use App\Entity\Enums\CheckingType;
use App\Entity\Enums\HttpMethod;
use App\Entity\NotificationReading;
use App\Service\StatusChecker\Checker\Strategy\ActualityChecking;
use App\Service\StatusChecker\Checker\Strategy\CheckingInterface;
use App\Service\StatusChecker\Checker\Strategy\OverallChecking;
use Exception;
use DateTime;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CheckerContext
{
    protected CheckingInterface $strategy;

    public function __construct(
        protected HttpClientInterface $client
    ) {
    }

    public function setStrategy(CheckingType $checkingType): self
    {
        $this->strategy = match ($checkingType) {
            CheckingType::ACTUALITY => new ActualityChecking(),
            CheckingType::OVERALL => new OverallChecking()
        };

        return $this;
    }

    public function check(
        HttpMethod $httpMethod,
        string $url
    ): NotificationReading {
        if (!isset($this->strategy)) {
            throw new Exception('Firstly set the checking strategy!');
        }

        $result = $this->strategy->check(
            $this->client->request(
                $httpMethod->value,
                $url
            )
        );

        return new NotificationReading(
            $result->httpStatus,
            $result->readingStatus,
            new DateTime(),
            $result->toArray()
        );
    }
}
