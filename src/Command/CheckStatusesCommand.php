<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\NotificationRepository;
use App\Service\StatusChecker\Checker\Checker;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'status-checker:check',
    description: 'Run status checking for all active requests.'
)]
class CheckStatusesCommand extends Command
{
    public function __construct(
        protected NotificationRepository $notificationRepository,
        protected Checker $checker,
        protected EntityManagerInterface $entityManager,
        protected LoggerInterface $logger
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->title('Starting process of checking statuses...');
        $notifications = $this->notificationRepository->getActiveNotificationRequests();
        $symfonyStyle->progressStart(count($notifications));
        foreach ($notifications as $notification) {
            $symfonyStyle->progressAdvance();
            try {
                $notification->addReading(
                    $this->checker
                        ->setStrategy(
                            $notification->getCheckingType()
                        )
                        ->check(
                            $notification->getHttpMethod(),
                            $notification->getUrl()
                        )
                );
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
        $symfonyStyle->newLine(2);
        $symfonyStyle->success('Process has been completed.');

        return Command::SUCCESS;
    }
}
