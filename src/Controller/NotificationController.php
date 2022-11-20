<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class NotificationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly NotificationRepository $notificationRepository
    ) {
    }

    #[Route('/notification', name: 'app_notification')]
    public function index(): Response
    {
        return $this->render('notification/index.html.twig', [
            'notifications' => $this->notificationRepository->findAll()
        ]);
    }

    #[Route('/notification/remove/{id}', name: 'app_notification_remove')]
    public function remove(Notification $notification): Response
    {
        $this->entityManager->remove($notification);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_notification');
    }

    #[Route('/notification/create', name: 'app_notification_create')]
    public function create(Request $request): Response
    {
        $notification = new Notification();
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($notification);
                $this->entityManager->flush();
            } catch (Throwable $throwable) {
                $this->addFlash(
                    'error',
                    'Something went wrong, error: ' . $throwable->getMessage()
                );

                return $this->redirectToRoute('app_notification_create');
            }

            return $this->redirectToRoute('app_notification');
        }

        return $this->render('notification/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/notification/edit/{id}', name: 'app_notification_edit')]
    public function edit(Request $request, Notification $notification): Response
    {
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($notification);
                $this->entityManager->flush();
            } catch (Throwable $throwable) {
                $this->addFlash(
                    'error',
                    'Something went wrong, error: ' . $throwable->getMessage()
                );

                return $this->redirectToRoute('app_notification_edit');
            }

            return $this->redirectToRoute('app_notification');
        }

        return $this->render('notification/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
