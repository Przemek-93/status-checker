<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NotificationRepository;
use App\Service\StatusChecker\Checker\Checker;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class StatusController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly NotificationRepository $notificationRepository,
        private readonly Checker $checker,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route(path: '/status', name: 'app_status')]
    public function index(): Response
    {
        return $this->render('status/index.html.twig', [
            'notifications' => $this->notificationRepository->getActiveNotificationWithReadings()
        ]);
    }

    #[Route(path: '/status/recheck', name: 'app_status_recheck')]
    public function recheck(): Response
    {
        $notifications = $this->notificationRepository->getActiveNotificationWithReadings();
        foreach ($notifications as $notification) {
            try {
                $notification->addReading($this->checker->check($notification));
            } catch (Throwable $throwable) {
                $this->logger->error(
                    sprintf(
                        'Error occurred while rechecking [id: %d, error: %s]',
                        $notification->getId(),
                        $throwable->getMessage()
                    )
                );
                $this->addFlash(
                    'error',
                    'Something went wrong while trying to recheck given check-requests.'
                );
            }
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('app_status', [
            'notifications' => $notifications
        ]);
    }
}
