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

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ManageCronCommand.
 *
 * This class handles adding and removing cronjobs.
 */
#[AsCommand(
    name: 'harmony:cron:manage',
    description: 'Adds or removes a cronjob',
)]
final class ManageCronCommand extends Command
{
    use LockableTrait;

    public function __construct(private string $cronFile = '/etc/cron.d/mediaease')
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
            ->setDescription('Adds or removes a cronjob.')
            ->addOption('action', null, InputOption::VALUE_REQUIRED, 'The action to perform (add/remove)')
            ->addOption('schedule', null, InputOption::VALUE_REQUIRED, 'The cron schedule (e.g. "* * * * *")')
            ->addOption('command', null, InputOption::VALUE_REQUIRED, 'The command to run');
    }

    /**
     * Executes the current command.
     *
     * @return int Command exit status
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('<error>The command is already running in another process.</error>');

            return Command::FAILURE;
        }

        $symfonyStyle = new SymfonyStyle($input, $output);
        $action = $input->getOption('action');
        $schedule = $input->getOption('schedule');
        $command = $input->getOption('command');

        if (!$action || !in_array($action, ['add', 'remove'], true)) {
            $symfonyStyle->error('Invalid action. Use "add" or "remove".');

            return Command::FAILURE;
        }

        if ($action === 'add' && (!$schedule || !$command)) {
            $symfonyStyle->error('Schedule and command are required for adding a cronjob.');

            return Command::FAILURE;
        }

        $this->ensureSudoersEntry($symfonyStyle);

        if ($action === 'add') {
            return $this->addCronjob($schedule, $command, $symfonyStyle);
        }

        return $this->removeCronjob($command, $symfonyStyle);
    }

    /**
     * Ensures the sudoers entry is present.
     */
    private function ensureSudoersEntry(SymfonyStyle $symfonyStyle): void
    {
        $process = new Process(['php', 'bin/console', 'harmony:sudoers', 'add']);
        $process->run();

        if (!$process->isSuccessful()) {
            $symfonyStyle->error('Failed to add sudoers entry: '.$process->getErrorOutput());
        } else {
            $symfonyStyle->success('Sudoers entry added successfully.');
        }
    }

    /**
     * Adds a cronjob.
     *
     * @return int Command exit status
     */
    private function addCronjob(string $schedule, string $command, SymfonyStyle $symfonyStyle): int
    {
        $cronLine = sprintf('%s %s', $schedule, $command);
        if (file_exists($this->cronFile)) {
            $existingCrons = file_get_contents($this->cronFile);
            if (str_contains($existingCrons, $command)) {
                $symfonyStyle->warning('Cronjob already exists.');

                return Command::FAILURE;
            }
        }

        file_put_contents($this->cronFile, $cronLine.PHP_EOL, FILE_APPEND);
        $symfonyStyle->success('Cronjob added successfully.');

        return Command::SUCCESS;
    }

    /**
     * Removes a cronjob.
     *
     * @return int Command exit status
     */
    private function removeCronjob(string $command, SymfonyStyle $symfonyStyle): int
    {
        if (!file_exists($this->cronFile)) {
            $symfonyStyle->warning('Cron file does not exist.');

            return Command::FAILURE;
        }

        $lines = file($this->cronFile, FILE_IGNORE_NEW_LINES);
        $newLines = array_filter($lines, static fn ($line): bool => !str_contains($line, $command));

        if (count($lines) === count($newLines)) {
            $symfonyStyle->warning('Cronjob not found.');

            return Command::FAILURE;
        }

        file_put_contents($this->cronFile, implode(PHP_EOL, $newLines).PHP_EOL);
        $symfonyStyle->success('Cronjob removed successfully.');

        return Command::SUCCESS;
    }
}
