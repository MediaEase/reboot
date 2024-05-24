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
