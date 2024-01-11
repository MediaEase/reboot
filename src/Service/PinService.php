<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Validator\PinServiceValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class PinService
{
    private const MAX_PINNED_APPS = 5;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private PinServiceValidator $pinServiceValidator
    ) {
    }

    public function handlePinning(User $user, ?int $serviceId): string
    {
        if ($serviceId === null) {
            throw new BadRequestHttpException('Service ID is required');
        }

        $this->pinServiceValidator->validateServiceBelongsToUser($user, $serviceId);

        $pinnedApps = $user->getPreference()->getPinnedApps();
        $pinnedServiceIds = array_column($pinnedApps, 'id');

        if (in_array($serviceId, $pinnedServiceIds, true)) {
            $this->unpinService($user, $serviceId, $pinnedApps);

            return 'unpin';
        }

        $this->pinService($user, $serviceId, $pinnedApps);

        return 'pin';
    }

    private function unpinService(User $user, int $serviceId, array &$pinnedApps): void
    {
        $pinnedAppIndex = array_search($serviceId, array_column($pinnedApps, 'id'), true);
        if ($pinnedAppIndex !== false) {
            array_splice($pinnedApps, $pinnedAppIndex, 1);
            $user->getPreference()->setPinnedApps($pinnedApps);
            $this->entityManager->persist($user->getPreference());
            $this->entityManager->flush();
        }
    }

    private function pinService(User $user, int $serviceId, array &$pinnedApps): void
    {
        if (count($pinnedApps) >= self::MAX_PINNED_APPS) {
            throw new BadRequestHttpException('Maximum number of pinned apps reached');
        }

        if (! in_array($serviceId, array_column($pinnedApps, 'id'), true)) {
            $pinnedApps[] = ['id' => $serviceId];
            $user->getPreference()->setPinnedApps($pinnedApps);
            $this->entityManager->persist($user->getPreference());
            $this->entityManager->flush();
        }
    }
}
