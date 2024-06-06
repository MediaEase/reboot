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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class TailLogCommand.
 *
 * This class handles the log:tail.
 */
#[AsCommand(
    name: 'log:tail',
    description: 'Create the MediaEase groups in the system',
)]
final class TailLogCommand extends Command
{
    /**
     * Configures the current command.
     *
     * This method is automatically called by Symfony before executing the command.
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Tail log file')
            ->addArgument('logfile', InputArgument::REQUIRED, 'The name of the log file to tail')
            ->setHelp('This command allows you to tail a log file');
    }

    /**
     * Executes the current command.
     *
     * @return int Command exit status
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logFile = $input->getArgument('logfile');

        if (preg_match('/\.log(\.\d+)?$/', $logFile) === 0 || preg_match('/\.log(\.\d+)?$/', $logFile) === 0 || preg_match('/\.log(\.\d+)?$/', $logFile) === false) {
            $output->writeln('<error>Invalid log file. Only .log or .log.[number] files are allowed.</error>');

            return Command::FAILURE;
        }

        if (!file_exists($logFile)) {
            $output->writeln('<error>Log file does not exist: '.$logFile.'</error>');

            return Command::FAILURE;
        }

        $phpExecutableFinder = new PhpExecutableFinder();
        $phpBinary = $phpExecutableFinder->find();

        if ($phpBinary === false) {
            $output->writeln('<error>PHP binary not found.</error>');

            return Command::FAILURE;
        }

        $projectRoot = dirname(__DIR__, 3);

        $process = new Process([$phpBinary, 'bin/console', 'log:manage-sudoers', 'add'], $projectRoot);
        try {
            $process->mustRun();
        } catch (ProcessFailedException $processFailedException) {
            $output->writeln('<error>Failed to add sudoers entry: '.$processFailedException->getMessage().'</error>');

            return Command::FAILURE;
        }

        $process = new Process(['sudo', 'tail', '-30', $logFile], $projectRoot);
        try {
            $process->mustRun();
            foreach ($process as $type => $data) {
                if ($type === Process::OUT) {
                    $output->write($data);
                } else {
                    $output->write('<error>'.$data.'</error>');
                }
            }
        } catch (ProcessFailedException $processFailedException) {
            $output->writeln('<error>'.$processFailedException->getMessage().'</error>');
        } finally {
            $process = new Process([$phpBinary, 'bin/console', 'log:manage-sudoers', 'remove'], $projectRoot);
            try {
                $process->mustRun();
                $output->writeln('<info>Sudoers entry removed successfully.</info>');
            } catch (ProcessFailedException $exception) {
                $output->writeln('<error>Failed to remove sudoers entry: '.$exception->getMessage().'</error>');
            }
        }

        return Command::SUCCESS;
    }
}
