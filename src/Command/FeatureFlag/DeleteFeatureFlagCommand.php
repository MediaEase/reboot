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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteFeatureFlagCommand.
 *
 * This class handles the deletefeatureflag.
 */
#[AsCommand(
    name: 'harmony:feature-flag:delete',
    description: 'Delete feature flag'
)]
final class DeleteFeatureFlagCommand extends AbstractFeatureFlagCommand
{
    /**
     * Configures the current command.
     *
     * This method is automatically called by Symfony before executing the command.
     */
    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the feature flag');
    }

    /**
     * Executes the current command.
     *
     * @return int Command exit status
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        $configFilePath = $this->getConfigFilePath();

        if (!file_exists($configFilePath)) {
            $symfonyStyle->error('Configuration file not found!');

            return Command::FAILURE;
        }

        $config = Yaml::parseFile($configFilePath);

        if (!isset($config['flagception']['features'][$name])) {
            $symfonyStyle->error(sprintf('Feature flag "%s" does not exist!', $name));

            return Command::FAILURE;
        }

        unset($config['flagception']['features'][$name]);

        file_put_contents($configFilePath, Yaml::dump($config, 4));

        $symfonyStyle->success(sprintf('Feature flag "%s" has been deleted.', $name));

        return Command::SUCCESS;
    }
}
