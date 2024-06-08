<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/services', name: 'api_services_')]
#[IsGranted('ROLE_USER')]
final class ServiceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ServiceRepository $serviceRepository
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        $services = $this->serviceRepository->findAll();

        return $this->json($services, Response::HTTP_OK, [], ['groups' => [Service::GROUP_GET_SERVICES]]);
    }
}