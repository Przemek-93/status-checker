<?php

declare(strict_types=1);

namespace App\Entity\Enums;

enum NotificationReadingsState: string
{
    case OPERATIONAL = 'OPERATIONAL';
    case WARNING = 'WARNING';
    case ERROR = 'ERROR';

    public function color(): string
    {
        return match ($this) {
            self::OPERATIONAL => 'green',
            self::WARNING => 'orange',
            self::ERROR => 'red'
        };
    }
}
