<?php

declare(strict_types=1);

/*
 * This file is part of the MediaEase project.
 *
 * (c) Thomas Chauveau <contact.tomc@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Security;

use App\Entity\User;
use App\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

final class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If method is GET and user is already logged in, redirect to home page
        if ($this->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            $this->addFlash('info', 'You are already logged in.');

            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error instanceof CustomUserMessageAccountStatusException) {
            $this->addFlash('error', $error->getMessageKey());

            return $this->redirectToRoute('app_login');
        }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $settings = $this->entityManager->getRepository(Setting::class)->findLast();

        return $this->render('pages/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'settings' => $settings,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Intercepted by the logout key on your firewall.');
    }
}
