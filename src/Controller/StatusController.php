<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatusController extends AbstractController
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository
    ) {
    }

    #[Route('/status', name: 'app_status')]
    public function index(): Response
    {
        return $this->render('status/index.html.twig', [
            'notifications' => $this->notificationRepository->getActiveNotificationRequests()
        ]);
    }
}
