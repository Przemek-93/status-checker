<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'status-checker:remove-readings',
    description: 'Remove redundant readings.'
)]
class RemoveRedundantReadingsCommand extends Command
{
    public function __construct(
        protected NotificationRepository $notificationRepository,
        protected EntityManagerInterface $entityManager,
        protected LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                name: 'readings-count',
                mode: InputOption::VALUE_REQUIRED,
                description: 'Number of readings to keep (ordered by date).',
                default: 30
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->title('Starting process of removing readings...');
        $readingsCount = $input->getOption('readings-count');
        $notifications = $this->notificationRepository->getActiveNotificationWithReadings();
        $symfonyStyle->progressStart(count($notifications));
        $removed = 0;
        foreach ($notifications as $notification) {
            try {
                $readings = $notification->getReadings();
                if ($readings->count() > $readingsCount) {
                    $symfonyStyle->progressAdvance();
                    foreach ($readings as $key => $reading) {
                        if ($key >= $readingsCount) {
                            $this->entityManager->remove($reading);
                            $removed++;
                        }
                    }
                }
            } catch (Throwable $throwable) {
                $this->logger->error(
                    sprintf(
                        'Something went wrong while removing reading, request id: %d, error: %s',
                        $notification->getId(),
                        $throwable->getMessage()
                    )
                );
            }
        }

        $this->entityManager->flush();
        $symfonyStyle->success(sprintf(
                'Process has been completed. [%d] readings has been removed.',
                $removed
        ));

        return Command::SUCCESS;
    }
}
