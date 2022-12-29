<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Checker;

use App\Entity\Enums\HttpResponseStatus;

class Response
{
    public function __construct(
        public readonly HttpResponseStatus $status,
        public readonly array $content
    ) {
    }

    public function toArray(): array
    {
        return [
            'statusCode' => $this->status,
            'responseContent' => $this->content
        ];
    }
}
