<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\NotificationRepository;
use App\Service\StatusChecker\Handler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'status-checker:execute',
    description: 'Execute process of checking-statuses and sends email notifications by added requests.'
)]
class ExecuteStatusCheckerCommand extends Command
{
    public function __construct(
        protected NotificationRepository $notificationRepository,
        protected Handler $checkerHandler,
        protected EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->title('Starting process of status-checking and notify...');
        $notifications = $this->notificationRepository->getAvailableForExecuteNotifications();
        $results = $this->checkerHandler->handle($notifications);
        $this->entityManager->flush();
        $found = count($notifications);
        $symfonyStyle->table(
            ['found', 'success', 'failed', 'sent'],
            [
                [
                    $found,
                    $found - ($results['failed'] + $results['sent']),
                    $results['failed'],
                    $results['sent']
                ]
            ]
        );
        $symfonyStyle->success('Process has been completed.');

        return Command::SUCCESS;
    }
}
