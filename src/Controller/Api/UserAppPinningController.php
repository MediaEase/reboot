<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\PinService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_me_')]
final class UserAppPinningController extends AbstractController
{
    public function __construct(
        private PinService $pinService
    ) {
    }

    #[Route('/me/my_apps', name: 'my_apps', methods: ['GET'])]
    public function getUserApps(#[CurrentUser] ?User $user, Request $request): JsonResponse
    {
        if (! $user instanceof \App\Entity\User) {
            return $this->json(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $serviceId = $data['service'] ?? null;

        try {
            $this->pinService->handlePinning($user, $serviceId);

            return $this->json(['message' => 'Pinned apps updated'], JsonResponse::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
