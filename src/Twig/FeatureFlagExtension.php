<?php

/*
 * This file is part of the MediaEase project.
 *
 * (c) Thomas Chauveau <contact.tomc@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Flagception\Manager\FeatureManagerInterface;

final class FeatureFlagExtension extends AbstractExtension
{
    public function __construct(
        private FeatureManagerInterface $featureManager,
    ){
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('feature_manager', [$this, 'getFeatureManager']),
            new TwigFunction('feature_flags', [$this, 'getFeatureFlags']),
        ];
    }

    public function getFeatureManager(): FeatureManagerInterface
    {
        return $this->featureManager;
    }

    public function getFeatureFlags(): array
    {
        return [
            'app_settings_extras_transcoding' => 'transcoding',
        ];
    }
}
