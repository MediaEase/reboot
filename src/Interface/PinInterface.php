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

namespace App\Interface;

use App\Entity\User;

interface PinInterface
{
    /**
     * Handles the pinning or unpinning of a service for a user.
     *
     * @param User     $user      the user for whom the pinning operation is being performed
     * @param int|null $serviceId the ID of the service to pin or unpin
     *
     * @return string returns 'pin' if the service was pinned, 'unpin' if the service was unpinned
     */
    public function handlePinning(User $user, ?int $serviceId): string;
}
