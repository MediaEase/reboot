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

namespace App\Controller\Site\Admin;

use App\Entity\User;
use App\Entity\Setting;
use App\Form\CreateUserType;
use App\Handler\RegistrationHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('', name: 'app_settings_')]
#[IsGranted('ROLE_ADMIN')]
class UsersController extends AbstractController
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private RegistrationHandler $registrationHandler
    ) {
    }

    #[Route('/users', name: 'users_list', methods: ['GET', 'POST'])]
    public function list(#[CurrentUser] User $user, Request $request): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $settings = $this->entityManager->getRepository(Setting::class)->findLast();
        $newUser = new User();
        $createUserForm = $this->createForm(CreateUserType::class, $newUser);
        $createUserForm->handleRequest($request);

        if ($createUserForm->isSubmitted() && $createUserForm->isValid()) {
            $plainPassword = $createUserForm->get('plainPassword')->getData();
            $this->registrationHandler->handleRegistration($newUser, $plainPassword, 'admin_creation');
            $this->addFlash('success', 'User created successfully.');

            return $this->redirectToRoute('app_settings_users_list');
        }

        return $this->render('pages/users/list/users.html.twig', [
            'users' => $this->sortUsers($users),
            'settings' => $settings,
            'user' => $user,
            'createUserForm' => $createUserForm->createView(),
        ]);
    }

    #[Route('/users/{id}/ban', name: 'ban_user', methods: ['GET'])]
    public function ban(User $user): Response
    {
        if ($user->isBanned() !== true) {
            $user->setBanned(true);
            $this->entityManager->flush();
            $this->addFlash('success', 'User banned successfully');
        }

        return $this->redirectToRoute('app_settings_users_list');
    }

    #[Route('/users/{id}/unban', name: 'unban_user', methods: ['GET'])]
    public function unban(User $user): Response
    {
        if ($user->isBanned() === true) {
            $user->setBanned(false);
            $this->entityManager->flush();
            $this->addFlash('success', 'User unbanned successfully');
        }

        return $this->redirectToRoute('app_settings_users_list');
    }

    #[Route('/users/{id}/delete', name: 'delete_user', methods: ['GET'])]
    public function delete(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->addFlash('success', 'User deleted successfully');

        return $this->redirectToRoute('app_settings_users_list');
    }

    /**
     * Sorts users by role and username.
     *
     * @param User[] $users
     */
    private function sortUsers(array $users): array
    {
        usort($users, static function ($a, $b): int {
            $aIsAdmin = in_array('ROLE_ADMIN', $a->getRoles(), true);
            $bIsAdmin = in_array('ROLE_ADMIN', $b->getRoles(), true);
            if ($aIsAdmin && !$bIsAdmin) {
                return -1;
            }

            if (!$aIsAdmin && $bIsAdmin) {
                return 1;
            }

            $aUsername = $a->getUsername();
            $bUsername = $b->getUsername();

            if (!is_string($aUsername)) {
                return 1;
            }

            if (!is_string($bUsername)) {
                return -1;
            }

            return strcmp($aUsername, $bUsername);
        });

        return $users;
    }
}
