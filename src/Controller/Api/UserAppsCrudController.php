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

use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/me/services', name: 'api_me_services_')]
#[IsGranted('ROLE_USER')]
final class UserAppsCrudController extends AbstractController
{
    public function __construct(
        private ServiceRepository $serviceRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, ServiceRepository $serviceRepository): Response
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
