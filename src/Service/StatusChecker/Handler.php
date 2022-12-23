<?php

declare(strict_types=1);

namespace App\Service\StatusChecker;

use App\Service\StatusChecker\Checker\Checker;
use App\Service\StatusChecker\Sender\SendData;
use App\Service\StatusChecker\Sender\Sender;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use DateTime;
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
        $results = ['failed' => 0, 'sent' => 0];
        $sendDataArray = [];
        foreach ($notifications as $notification) {
            try {
                $reading = $this->checker->check($notification);
                $notification->addReading($reading);
                if ($reading->isFailed()) {
                    foreach ($notification->getReceivers() as $receiver) {
                        $email = $receiver->getEmail();
                            $sendDataArray[$email][] = new SendData(
                                $notification,
                                $reading,
                                $email
                            );
                    }
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

        foreach ($sendDataArray as $receiver => $array) {
            $this->sender->send(
                ['data' => $array],
                'emails/alert.html.twig',
                $receiver,
                'Status-checker ALERT! - ' .
                (new DateTime())->format('Y-m-d H:i:s')
            );
            $results['sent']++;
        }

        return $results;
    }
}
