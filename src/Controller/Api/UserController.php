<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users', name: 'api_user_')]
final class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository
    ) {
    }

    #[Route('/{id}', name: 'getUser', methods: ['GET'])]
    public function getUserInfo(User $user): Response
    {
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:info']);
    }

    #[Route('/{id}', name: 'updateUser', methods: ['PUT'])]
    public function updateUser(User $user): Response
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User deleted'], Response::HTTP_OK);
    }
}
