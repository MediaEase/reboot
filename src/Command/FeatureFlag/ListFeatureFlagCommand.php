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
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListFeatureFlagCommand.
 *
 * This class handles the listfeatureflag.
 */
#[AsCommand(
    name: 'harmony:feature-flag:list',
    description: 'List feature flag'
)]
final class ListFeatureFlagCommand extends AbstractFeatureFlagCommand
{
    /**
     * Configures the current command.
     *
     * This method is automatically called by Symfony before executing the command.
     */
    protected function configure(): void
    {
    }

    /**
     * Executes the current command.
     *
     * @return int Command exit status
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $configFilePath = $this->getConfigFilePath();

        if (!file_exists($configFilePath)) {
            $symfonyStyle->error('Configuration file not found!');

            return Command::FAILURE;
        }

        $config = Yaml::parseFile($configFilePath);

        if (empty($config['flagception']['features'])) {
            $symfonyStyle->warning('No feature flags found.');

            return Command::SUCCESS;
        }

        $symfonyStyle->table(
            ['Name', 'Default', 'Constraint'],
            array_map(static fn ($name, $details): array => [
                'name' => $name,
                'default' => $details['default'] ? 'true' : 'false',
                'constraint' => $details['constraint'] ?? 'None',
            ], array_keys($config['flagception']['features']), $config['flagception']['features'])
        );

        return Command::SUCCESS;
    }
}
