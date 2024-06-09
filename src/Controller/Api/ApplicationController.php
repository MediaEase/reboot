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

namespace App\Controller\Api;

use App\Entity\Store;
use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/apps', name: 'api_apps_')]
#[IsGranted('ROLE_USER')]
final class ApplicationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ApplicationRepository $applicationRepository
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        $applications = $this->applicationRepository->findApplicationsWithStores();

        return $this->json($applications, Response::HTTP_OK, [], ['groups' => [Application::GROUP_GET_APPLICATIONS, Store::GROUP_GET_STORES]]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Application $application): Response
    {
        $this->entityManager->persist($application);
        $this->entityManager->flush();

        return $this->json(['message' => 'Application created'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Application $application): Response
    {
        return $this->json($application, Response::HTTP_OK, [], [Application::GROUP_GET_APPLICATIONS, Store::GROUP_GET_STORES]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Application $application): Response
    {
        $this->entityManager->remove($application);
        $this->entityManager->flush();

        return $this->json(['message' => 'Application deleted'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Application $application): Response
    {
        $this->entityManager->persist($application);
        $this->entityManager->flush();

        return $this->json(['message' => 'Application updated'], Response::HTTP_OK);
    }
}
