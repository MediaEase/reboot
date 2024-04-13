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

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'confidential']);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(User $user): Response
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delate', methods: ['DELETE'])]
    public function deleteUser(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User deleted'], Response::HTTP_OK);
    }
}
