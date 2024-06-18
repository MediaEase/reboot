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

namespace App\Command\General;

use App\Entity\Store;
use App\Entity\Application;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ScanAppsCommand.
 *
 * This class handles the app:scan.
 */
#[AsCommand(
    name: 'harmony:scan:apps',
    description: 'Scans the software directories and populates the database with applications',
)]
final class ScanAppsCommand extends Command
{
    private int $addedCount = 0;

    private int $skippedCount = 0;

    private int $updatedCount = 0;

    private int $invalidCount = 0;

    private array $addedApps = [];

    private array $skippedApps = [];

    private array $updatedApps = [];

    private array $invalidApps = [];

    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    /**
     * Configures the current command.
     *
     * This method is automatically called by Symfony before executing the command.
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Scans the software directories and populates the database with applications.')
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'The base path to scan', '/home/thomas/prout/scripts/src/software')
            ->addOption('update', null, InputOption::VALUE_NONE, 'Update existing applications instead of creating new entries');
    }

    /**
     * Executes the current command.
     *
     * @return int Command exit status
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $basePath = $input->getOption('path');
        $directories = ['custom', 'experimental', 'official'];

        foreach ($directories as $directory) {
            $path = $basePath.DIRECTORY_SEPARATOR.$directory;
            if (is_dir($path)) {
                $this->scanDirectory($path, $output, $input->getOption('update'));
            }
        }

        $this->entityManager->flush();
        sort($this->addedApps);
        sort($this->updatedApps);
        sort($this->skippedApps);
        $returnStringParts = [];
        if ($this->addedCount > 0) {
            $output->writeln('Added apps: '.implode(', ', $this->addedApps));
            $returnStringParts[] = $this->addedCount.' apps added';
        }

        if ($this->updatedCount > 0) {
            $output->writeln('Updated apps: '.implode(', ', $this->updatedApps));
            $returnStringParts[] = $this->updatedCount.' apps updated';
        }

        if ($this->skippedCount > 0) {
            $output->writeln('Skipped apps: '.implode(', ', $this->skippedApps));
            $returnStringParts[] = $this->skippedCount.' apps skipped';
        }

        if ($returnStringParts !== []) {
            $output->writeln('Scan complete. '.implode(', ', $returnStringParts));
        }

        if ($this->invalidCount > 0) {
            $output->writeln($this->invalidCount.' invalid apps found. Please check the following apps: '.implode(', ', $this->invalidApps));
        }

        return Command::SUCCESS;
    }

    /**
     * Scans the directory for config.yml files and creates or updates the application entity.
     */
    private function scanDirectory(string $path, OutputInterface $output, bool $update): void
    {
        $dirIterator = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($dirIterator);
        foreach ($iterator as $file) {
            if ($file->getFilename() === 'config.yaml') {
                $config = Yaml::parseFile($file->getPathname());
                if ($this->validateConfig($config, $output)) {
                    $this->createOrUpdateApplicationEntity($config, $update);
                } else {
                    ++$this->invalidCount;
                    $this->invalidApps[] = $config['arguments']['app_name'];
                    $output->writeln('Skipping invalid config.yaml in '.$file->getPath());
                }
            }
        }
    }

    /**
     * Validates the config array.
     */
    private function validateConfig(array $config, OutputInterface $output): bool
    {
        $requiredKeys = [
            'arguments' => ['app_name', 'altname', 'description', 'pro_only', 'logo_path', 'multi_user', 'ports', 'service_directives', 'files', 'paths', 'group', 'details'],
        ];

        foreach ($requiredKeys as $parentKey => $keys) {
            if (!isset($config[$parentKey])) {
                $output->writeln('Missing required parent key: '.$parentKey);

                return false;
            }

            foreach ($keys as $key) {
                if (!isset($config[$parentKey][$key])) {
                    $output->writeln('Missing required key: '.$parentKey.'.'.$key);

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Creates or updates the application entity.
     */
    private function createOrUpdateApplicationEntity(array $config, bool $update): void
    {
        $entityRepository = $this->entityManager->getRepository(Application::class);
        $existingApplication = $entityRepository->findOneBy(['name' => $config['arguments']['app_name']]);
        if ($existingApplication) {
            if ($update) {
                $existingApplication
                    ->setAltname($config['arguments']['altname'])
                    ->setLogo($config['arguments']['logo_path'])
                ;
                $existingApplication
                    ->getStore()
                        ->setDescription($config['arguments']['description'])
                        ->setIsPro($config['arguments']['pro_only'])
                        ->setIsAvailable(true)
                        ->setType($config['arguments']['group'])
                        ->setDetails($config['arguments']['details'])
                        ->setIsMultiUser($config['arguments']['multi_user'])
                ;


                ++$this->updatedCount;
                $this->updatedApps[] = $config['arguments']['app_name'];
            } else {
                ++$this->skippedCount;
                $this->skippedApps[] = $config['arguments']['app_name'];
            }
        } else {
            $application = new Application();
            $application
                ->setName($config['arguments']['app_name'])
                ->setAltname($config['arguments']['altname'])
                ->setLogo($config['arguments']['logo_path'])
            ;

            $store = new Store();
            $store
                ->setDescription($config['arguments']['description'])
                ->setIsPro($config['arguments']['pro_only'])
                ->setIsAvailable(true)
                ->setType($config['arguments']['group'])
                ->setApplication($application)
                ->setDetails($config['arguments']['details'])
                ->setMultiUser($config['arguments']['multi_user'])
            ;

            $application->setStore($store);

            $this->entityManager->persist($application);
            $this->entityManager->persist($store);

            ++$this->addedCount;
            $this->addedApps[] = $config['arguments']['app_name'];
        }
    }
}
