<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Preference;
use App\Entity\User;
use App\Validator\PinServiceValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Service class to manage pinning and unpinning services for users.
 */
final class PinService
{
    private const MAX_PINNED_APPS = 5;

    /**
     * Constructor for the PinService.
     *
     * @param EntityManagerInterface $entityManager EntityManager to interact with the database.
     * @param PinServiceValidator $pinServiceValidator Validator for service pinning operations.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PinServiceValidator $pinServiceValidator
    ) {
    }

    /**
     * Handles the pinning or unpinning of a service for a user.
     *
     * @param User $user The user for whom the pinning operation is being performed.
     * @param int|null $serviceId The ID of the service to pin or unpin.
     * @return string Returns 'pin' if the service was pinned, 'unpin' if the service was unpinned.
     * @throws BadRequestHttpException If the service ID is missing or invalid operations are performed.
     */
    public function handlePinning(User $user, ?int $serviceId): string
    {
        if ($serviceId === null) {
            throw new BadRequestHttpException('Service ID is required');
        }

        $this->pinServiceValidator->validateServiceBelongsToUser($user, $serviceId);

        $pinnedApps = $this->getUserPreferences($user)->getPinnedApps();
        $pinnedServiceIds = array_column($pinnedApps, 'id');

        if (in_array($serviceId, $pinnedServiceIds, true)) {
            $this->unpinService($user, $serviceId, $pinnedApps);

            return 'unpin';
        }

        $this->pinService($user, $serviceId, $pinnedApps);

        return 'pin';
    }

    /**
     * Removes a service from the user's pinned applications.
     *
     * @param User $user User who owns the pinned apps.
     * @param int $serviceId ID of the service to be unpinned.
     * @param array $pinnedApps Array of currently pinned apps.
     */
    private function unpinService(User $user, int $serviceId, array &$pinnedApps): void
    {
        $pinnedAppIndex = array_search($serviceId, array_column($pinnedApps, 'id'), true);
        if ($pinnedAppIndex !== false) {
            array_splice($pinnedApps, $pinnedAppIndex, 1);
            $newPreferences = $this->getUserPreferences($user)->setPinnedApps($pinnedApps);
            $this->entityManager->persist($newPreferences);
            $this->entityManager->flush();
        }
    }

    /**
     * Adds a service to the user's pinned applications.
     *
     * @param User $user User who will pin the app.
     * @param int $serviceId ID of the service to be pinned.
     * @param array $pinnedApps Array of currently pinned apps.
     * @throws BadRequestHttpException If the maximum number of pinned apps is exceeded.
     */
    private function pinService(User $user, int $serviceId, array &$pinnedApps): void
    {
        if (count($pinnedApps) >= self::MAX_PINNED_APPS) {
            throw new BadRequestHttpException('Maximum number of pinned apps reached');
        }

        if (!in_array($serviceId, array_column($pinnedApps, 'id'), true)) {
            $pinnedApps[] = ['id' => $serviceId];
            $this->getUserPreferences($user)->setPinnedApps($pinnedApps);
            $this->entityManager->persist($user->getPreferences());
            $this->entityManager->flush();
        }
    }

    /**
     * Retrieves the user's preference entity.
     *
     * @param User $user The user whose preferences are being retrieved.
     * @return Preference|null The user's preferences, or null if not found.
     */
    private function getUserPreferences(User $user): ?Preference
    {
        return $this->entityManager->getRepository(Preference::class)->findOneBy(['user' => $user->getId()]);
    }
}
