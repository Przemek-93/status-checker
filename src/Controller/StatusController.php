<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NotificationRepository;
use App\Service\StatusChecker\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatusController extends AbstractController
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository,
        private readonly Handler $checkerHandler
    ) {
    }

    #[Route('/status', name: 'app_status')]
    public function index(): Response
    {
        return $this->render('status/index.html.twig', [
            'notifications' => $this->notificationRepository->getActiveNotificationWithReadings()
        ]);
    }

    #[Route('/status/recheck', name: 'app_status_recheck')]
    public function recheck(): Response
    {
        $checkedNotifications = $this->checkerHandler->bulkChecking(
            $this->notificationRepository->getActiveNotificationWithReadings()
        );

        return $this->redirectToRoute('app_status', [
            'notifications' => $checkedNotifications
        ]);
    }
}
