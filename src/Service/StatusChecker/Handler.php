<?php

declare(strict_types=1);

namespace App\Service\StatusChecker;

use App\Service\StatusChecker\Checker\Checker;
use App\Service\StatusChecker\Sender\SendData;
use App\Service\StatusChecker\Sender\Sender;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class Handler
{
    public function __construct(
        protected readonly Checker $checker,
        protected readonly LoggerInterface $logger,
        protected readonly Sender $sender,
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    public function handle(array $notifications): array
    {
        $results = ['success' => 0, 'failed' => 0];
        $sendDataArray = [];
        foreach ($notifications as $notification) {
            try {
                $reading = $this->checker->check($notification);
                $notification->addReading($reading);
                $results['success']++;
                foreach ($notification->getReceivers() as $receiver) {
                    $sendDataArray[$receiver->getEmail()][] = new SendData($notification, $reading);
                }
                $notification->setSendingDate();
            } catch (Throwable $throwable) {
                $results['failed']++;
                $this->logger->error(
                    sprintf(
                        'Something went wrong while processing notification request [url: %s, id: %d],
                        error: [%s]',
                        $notification->getUrl(),
                        $notification->getId(),
                        $throwable->getMessage()
                    )
                );
            }
        }

        $this->sender->send($sendDataArray);

        return $results;
    }

    public function bulkChecking(array $notifications): array
    {
        foreach ($notifications as $notification) {
            try {
                $notification->addReading($this->checker->check($notification));
            } catch (Throwable $throwable) {
                $this->logger->error(
                    sprintf(
                        'Something went wrong while checking status, request id: %d, error: %s',
                        $notification->getId(),
                        $throwable->getMessage()
                    )
                );
            }
        }

        $this->entityManager->flush();

        return $notifications;
    }
}
