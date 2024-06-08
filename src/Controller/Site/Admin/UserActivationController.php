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
use App\Handler\RegistrationHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]
class UserActivationController extends AbstractController
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private RegistrationHandler $registrationHandler
    ) {
    }

    #[Route('/activate/{id}', name: 'activate_user', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function activate(User $user): Response
    {
        if ($user->getActivatedAt() instanceof \DateTimeImmutable) {
            return $this->redirectToRoute('admin_user_list');
        }

        $this->registrationHandler->activateUser($user);
        $this->addFlash('success', 'User activated successfully');

        return $this->redirectToRoute('admin_user_list');
    }
}
