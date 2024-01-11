<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api', name: 'api_apps_')]
#[IsGranted('ROLE_USER')]
final class StoreController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StoreRepository $storeRepository
    ) {
    }

    #[Route('/store', name: 'getStore', methods: ['GET'])]
    public function getStore(): Response
    {
        $stores = $this->storeRepository->findAll();

        return $this->json($stores, Response::HTTP_OK, [], ['groups' => 'store:info']);
    }
}
