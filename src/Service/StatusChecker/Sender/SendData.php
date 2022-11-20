<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Sender;

use App\Entity\Notification;
use App\Entity\NotificationReading;
use Exception;

class SendData
{
    public function __construct(
        protected Notification $notification,
        protected NotificationReading $reading
    ) {
        if ($this->reading->getNotificationId() !== $this->notification->getId()) {
            throw new Exception('Reading and notifications are not related.');
        }
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }

    public function getReading(): NotificationReading
    {
        return $this->reading;
    }

    public function isReadingFailed(): bool
    {
        if ($this->reading->isFailed()) {
            return true;
        }

        return false;
    }
}
