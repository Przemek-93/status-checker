<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Checker\Strategy;

use App\Entity\Enums\HttpResponseStatus;
use App\Entity\Enums\ReadingStatus;
use App\Service\StatusChecker\Checker\Result;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OverallChecking implements CheckingInterface
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * Avoid using static access to class HttpResponseStatus.
     */
    public function check(ResponseInterface $response): Result
    {
        $httpStatus = HttpResponseStatus::from(
            $response->getStatusCode()
        );

        return new Result(
            $httpStatus,
            $httpStatus->isFailed() ? ReadingStatus::FAILED : ReadingStatus::SUCCESS,
            $response->getContent(false)
        );
    }
}
