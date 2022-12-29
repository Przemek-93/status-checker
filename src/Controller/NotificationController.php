<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class NotificationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly NotificationRepository $notificationRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route(path: '/notification', name: 'app_notification')]
    public function index(): Response
    {
        return $this->render('notification/index.html.twig', [
            'notifications' => $this->notificationRepository->findAll()
        ]);
    }

    #[Route(
        path: '/notification/remove/{id}',
        name: 'app_notification_remove',
        requirements: ['id' => '\d+']
    )]
    public function remove(int $id): Response
    {
        try {
            $notification = $this->notificationRepository->find($id);
            $this->entityManager->remove($notification);
            $this->entityManager->flush();
        } catch (Throwable $throwable) {
            $this->logger->error(
                sprintf(
                    'Error occurred while removing notification [id: %d, error: %s]',
                    $id,
                    $throwable->getMessage()
                )
            );
            $this->addFlash(
                'error',
                'Something went wrong while trying to remove given check-request.'
            );

            return $this->redirectToRoute('app_notification');
        }


        return $this->redirectToRoute('app_notification');
    }

    #[Route(path: '/notification/create', name: 'app_notification_create')]
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
                $this->logger->error(
                    sprintf(
                        'Error occurred while creating notification [error: %s]',
                        $throwable->getMessage()
                    )
                );
                $this->addFlash(
                    'error',
                    'Something went wrong while trying to create check-request.'
                );

                return $this->redirectToRoute('app_notification_create');
            }

            return $this->redirectToRoute('app_notification');
        }

        return $this->render('notification/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        path: '/notification/edit/{id}',
        name: 'app_notification_edit',
        requirements: ['id' => '\d+']
    )]
    public function edit(Request $request, int $id): Response
    {
        $notification = $this->notificationRepository->find($id);
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($notification);
                $this->entityManager->flush();
            } catch (Throwable $throwable) {
                $this->logger->error(
                    sprintf(
                        'Error occurred while editing notification [id: %d, error: %s]',
                        $id,
                        $throwable->getMessage()
                    )
                );
                $this->addFlash(
                    'error',
                    'Something went wrong while trying to edit check-request.'
                );

                return $this->redirectToRoute(
                    'app_notification_edit',
                    ['id' => $id]
                );
            }

            return $this->redirectToRoute('app_notification');
        }

        return $this->render('notification/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
