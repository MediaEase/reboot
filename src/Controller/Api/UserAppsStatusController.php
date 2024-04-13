<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/me/services/status', name: 'api_me_services_')]
#[IsGranted('ROLE_USER')]
final class UserAppsStatusController extends AbstractController
{
    public function __construct(
        private ServiceRepository $serviceRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'status', methods: ['GET'])]
    public function update(): Response
    {
        $services = $this->serviceRepository->findBy(['user' => $this->getUser()]);

        foreach ($services as $service) {
            $process = new Process(['systemctl', 'is-active', $service->getName()]);
            $process->run();
            $status = $process->isSuccessful() ? 'active' : 'inactive';
            $service->setStatus($status);
            $this->entityManager->persist($service);
        }

        $this->entityManager->flush();

        return $this->json(['message' => 'Service statuses updated']);
    }
}
