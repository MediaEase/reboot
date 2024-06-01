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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('', name: 'app_settings_')]
#[IsGranted('ROLE_ADMIN')]
class UsersController extends AbstractController
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/users', name: 'users_list', methods: ['GET'])]
    public function list(#[CurrentUser] User $user): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $settings = $this->entityManager->getRepository(Setting::class)->findLast();

        return $this->render('pages/users/list/users.html.twig', [
            'users' => $users,
            'settings' => $settings,
            'user' => $user,
        ]);
    }
}
