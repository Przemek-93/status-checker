<?php

declare(strict_types=1);

namespace App\Service\StatusChecker\Sender;

use App\Entity\Notification;
use App\Entity\NotificationReading;
use Exception;
use DateTime;

class SendData
{
    public function __construct(
        protected Notification $notification,
        protected NotificationReading $reading,
        protected string $receiverEmail
    ) {
        if ($this->reading->getNotificationId() !== $this->notification->getId()) {
            throw new Exception('Reading and notifications are not related.');
        }
    }

    public function getNotificationUrl(): ?string
    {
        return $this->notification->getUrl();
    }

    public function getReadingStatus(): ?int
    {
        return $this->reading->getStatus();
    }

    public function getReadAt(): ?DateTime
    {
        return $this->reading->getReadAt();
    }
}
