<?php

declare(strict_types=1);

namespace App\Entity\Enums;

enum NotificationReadingsState: string
{
    case OPERATIONAL = 'OPERATIONAL';
    case WARNING = 'WARNING';
    case NOT_FRESH = 'NOT FRESH';
    case ERROR = 'ERROR';

    public function color(): string
    {
        return match ($this) {
            self::OPERATIONAL => 'green',
            self::WARNING, self::NOT_FRESH => 'orange',
            self::ERROR => 'red'
        };
    }
}
