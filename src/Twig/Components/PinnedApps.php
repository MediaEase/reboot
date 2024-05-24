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

namespace App\Twig\Components;

use App\Repository\PreferenceRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('PinnedApps')]
final class PinnedApps
{
    use DefaultActionTrait;

    public function __construct(private PreferenceRepository $preferenceRepository, private Security $security, private ServiceRepository $serviceRepository)
    {
    }

    /**
     * @return array<string, string>
     */
    public function apps(): array
    {
        $user = $this->security->getUser();

        if (! $this->security->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            return [];
        }

        $preference = $this->preferenceRepository->findOneBy(['user' => $user]);
        $pinnedAppIds = $preference->getPinnedApps();

        $pinnedApps = [];
        foreach ($pinnedAppIds as $pinnedAppId) {
            $service = $this->serviceRepository->find($pinnedAppId);
            if ($service !== null) {
                $pinnedApps[] = $service;
            }
        }

        return $pinnedApps;
    }
}
