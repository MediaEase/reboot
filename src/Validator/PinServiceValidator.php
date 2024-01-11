<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\User;
use App\Repository\ServiceRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class PinServiceValidator
{
    public function __construct(
        private ServiceRepository $serviceRepository
    ) {
    }

    public function validateServiceBelongsToUser(User $user, int $serviceId): void
    {
        $service = $this->serviceRepository->findOneBy(['id' => $serviceId, 'user' => $user]);
        if ($service === null) {
            throw new BadRequestHttpException('Service does not belong to the user');
        }
    }
}
