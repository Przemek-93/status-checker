<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Checker\Strategy;

use App\Service\StatusChecker\Checker\Result;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface CheckingInterface
{
    public function check(ResponseInterface $response): Result;
}
