<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Checker;

use App\Entity\Enums\HttpResponseStatus;
use App\Entity\Enums\ReadingStatus;

class Result
{
    protected array $content;

    public function __construct(
        public readonly HttpResponseStatus $httpStatus,
        public readonly ReadingStatus $readingStatus,
        string $jsonContent
    ) {
        if (mb_strlen($jsonContent) >= 2000) {
            $jsonContent = mb_substr($jsonContent, 0, 2000) . '...';
        }

        $this->content = json_decode($jsonContent, true) ?? [trim($jsonContent)];
    }

    public function toArray(): array
    {
        return [
            'httpStatusCode' => $this->httpStatus,
            'readingsStatus' => $this->readingStatus,
            'responseContent' => $this->content
        ];
    }
}
