<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/registration', name: 'app_registration')]
    public function index(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user
                    ->setPassword(
                        $this->passwordHasher->hashPassword(
                            $user,
                            $user->getPassword()
                        )
                    )
                    ->setRoles(['ROLE_USER']);

                $this->entityManager->persist($user);
                $this->entityManager->flush();
            } catch (Throwable $throwable) {
                $this->logger->error(
                    sprintf(
                        'Error occurred while creating user [error: %s]',
                        $throwable->getMessage()
                    )
                );
                $this->addFlash(
                    'error',
                    'Something went wrong while trying to register new user.'
                );

                return $this->redirectToRoute('app_registration');
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
