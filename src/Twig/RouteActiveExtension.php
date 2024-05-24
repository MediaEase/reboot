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

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\RequestStack;

final class RouteActiveExtension extends AbstractExtension
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_active_route', [$this, 'isActiveRoute']),
        ];
    }

    public function isActiveRoute(string $routeName): bool
    {
        $currentRoute = $this->requestStack->getCurrentRequest()->attributes->get('_route');

        return $currentRoute === $routeName;
    }
}
