<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Checker;

class Response
{
    public function __construct(
        private readonly int $status,
        private readonly array $content
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function toArray(): array
    {
        return [
            'statusCode' => $this->status,
            'responseContent' => $this->content
        ];
    }
}
