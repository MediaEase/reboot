<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/me/services', name: 'api_me_')]
#[IsGranted('ROLE_USER')]
final class UserAppsCrudController extends AbstractController
{
    public function __construct(
        private ServiceRepository $serviceRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/{id}', name: 'deleteService', methods: ['DELETE'])]
    public function deleteService(int $id, ServiceRepository $serviceRepository): Response
    {
        $service = $this->serviceRepository->findOneBy(['id' => $id]);

        if ($service === null) {
            return $this->json(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        if ($service->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($service);
        $this->entityManager->flush();

        return $this->json(['message' => 'Service deleted'], Response::HTTP_OK);
    }
}