<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/apps', name: 'api_apps_')]
#[IsGranted('ROLE_USER')]
final class ApplicationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ApplicationRepository $applicationRepository
    ) {
    }

    #[Route('', name: 'getApplications', methods: ['GET'])]
    public function getApplications(): Response
    {
        $applications = $this->applicationRepository->findAll();

        return $this->json($applications, Response::HTTP_OK, [], ['groups' => 'application:info']);
    }

    #[Route('', name: 'createApplication', methods: ['POST'])]
    public function createApplication(Application $application): Response
    {
        $this->entityManager->persist($application);
        $this->entityManager->flush();

        return $this->json(['message' => 'Application created'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'getApplication', methods: ['GET'])]
    public function getApplication(Application $application): Response
    {
        return $this->json($application, Response::HTTP_OK, [], ['groups' => 'application:info']);
    }

    #[Route('/{id}', name: 'deleteApplication', methods: ['DELETE'])]
    public function deleteApplication(Application $application): Response
    {
        $this->entityManager->remove($application);
        $this->entityManager->flush();

        return $this->json(['message' => 'Application deleted'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'updateApplication', methods: ['PUT'])]
    public function updateApplication(Application $application): Response
    {
        $this->entityManager->persist($application);
        $this->entityManager->flush();

        return $this->json(['message' => 'Application updated'], Response::HTTP_OK);
    }
}
