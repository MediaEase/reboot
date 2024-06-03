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

namespace App\Command\FeatureFlag;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

abstract class AbstractFeatureFlagCommand extends Command
{
    public function __construct(private ContainerBagInterface $containerBag)
    {
        parent::__construct();
    }

    /**
     * Get the configuration file path.
     */
    protected function getConfigFilePath(): string
    {
        return $this->containerBag->get('kernel.project_dir').'/config/packages/flagception.yaml';
    }

    /**
     * Parse the YAML configuration file.
     */
    protected function parseConfigFile(): array
    {
        $configFilePath = $this->getConfigFilePath();

        if (!file_exists($configFilePath)) {
            throw new \RuntimeException('Configuration file not found!');
        }

        return Yaml::parseFile($configFilePath);
    }

    /**
     * Save the configuration to the YAML file.
     */
    protected function saveConfigFile(array $config): void
    {
        file_put_contents($this->getConfigFilePath(), Yaml::dump($config, 4));
    }
}
