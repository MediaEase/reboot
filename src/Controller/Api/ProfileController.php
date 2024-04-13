<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Preference;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/me', name: 'api_me_')]
#[IsGranted('ROLE_USER')]
final class ProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository
    ) {
    }

    #[Route('', name: 'my_profile', methods: ['GET'])]
    public function show(#[CurrentUser] User $user): Response
    {
        $profile = $this->userRepository->findMyProfile($user);

        return $this->json($profile, Response::HTTP_OK, [], ['groups' => [User::GROUP_GET_USER_LIMITED, Preference::GROUP_GET_PREFERENCES]]);
    }
}
