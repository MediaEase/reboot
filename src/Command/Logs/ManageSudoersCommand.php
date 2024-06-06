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

namespace App\Command\Logs;

use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Class ManageSudoersCommand.
 *
 * This class handles the log:manage-sudoers.
 */
#[AsCommand(
    name: 'log:manage-sudoers',
    description: 'Create the MediaEase groups in the system',
)]
final class ManageSudoersCommand extends Command
{
    /**
     * Configures the current command.
     *
     * This method is automatically called by Symfony before executing the command.
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Add or remove sudoers entry for log tailing')
            ->addArgument('action', InputArgument::REQUIRED, 'add or remove');
    }

    /**
     * Executes the current command.
     *
     * @return int Command exit status
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = $input->getArgument('action');
        $filesystem = new Filesystem();
        $sudoersFile = '/etc/sudoers.d/www-data-tail';
        $tempSudoersFile = '/tmp/www-data-tail';

        if ($action === 'add') {
            $entry = "www-data ALL=(ALL) NOPASSWD: /usr/bin/cat /var/log/*.log /var/log/*.log.*\n";

            try {
                $filesystem->dumpFile($tempSudoersFile, $entry);

                $process = new Process(['sudo', 'chown', 'root:root', $tempSudoersFile]);
                $process->mustRun();
                $process = new Process(['sudo', 'chmod', '0440', $tempSudoersFile]);
                $process->mustRun();
                $process = new Process(['sudo', 'mv', $tempSudoersFile, $sudoersFile]);
                $process->mustRun();

                $output->writeln('<info>Sudoers entry added successfully.</info>');
            } catch (IOExceptionInterface $exception) {
                $output->writeln('<error>Failed to add sudoers entry: '.$exception->getMessage().'</error>');

                return Command::FAILURE;
            }
        } elseif ($action === 'remove') {
            $process = new Process(['sudo', 'rm', '-f', $sudoersFile]);

            try {
                $process->mustRun();
                $output->writeln('<info>Sudoers entry removed successfully.</info>');
            } catch (ProcessFailedException $exception) {
                $output->writeln('<error>Failed to remove sudoers entry: '.$exception->getMessage().'</error>');

                return Command::FAILURE;
            }
        } else {
            $output->writeln('<error>Invalid action. Use "add" or "remove".</error>');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
