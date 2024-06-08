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

namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Psr\Log\LoggerInterface;

/**
 * Class CommandExecutorService.
 *
 * This class handles the execution of commands.
 */
final class CommandExecutorService
{
    private string $projectRoot;

    private string $phpBinary;

    public function __construct(private LoggerInterface $logger)
    {
        $this->projectRoot = dirname(__DIR__, 3);
        $phpExecutableFinder = new PhpExecutableFinder();
        $this->phpBinary = $phpExecutableFinder->find();

        if ($this->phpBinary === false) {
            throw new \RuntimeException('PHP binary not found.');
        }
    }

    /**
     * Execute a command with optional arguments.
     *
     * @param string $command   The command to execute
     * @param array  $arguments The arguments for the command
     *
     * @throws \Exception
     */
    public function execute(string $command, array $arguments = []): void
    {
        try {
            $this->manageSudoersEntry('add');
            if (str_starts_with($command, 'zen')) {
                $command = '/usr/bin/'.$command;
            }

            $this->runProcess(['sudo', $command, ...$arguments]);
        } catch (ProcessFailedException $processFailedException) {
            $this->logger->error('Command execution failed', ['exception' => $processFailedException]);
            throw $processFailedException;
        } finally {
            try {
                $this->manageSudoersEntry('remove');
            } catch (ProcessFailedException $exception) {
                $this->logger->error('Failed to remove sudoers entry', ['exception' => $exception]);
            }
        }
    }

    /**
     * Manage sudoers entry for the current process.
     *
     * @param string $action The action to perform (add/remove)
     *
     * @throws ProcessFailedException
     */
    private function manageSudoersEntry(string $action): void
    {
        $process = new Process([$this->phpBinary, 'bin/console', 'log:manage-sudoers', $action], $this->projectRoot);
        $process->mustRun();
    }

    /**
     * Run a process and handle its output.
     *
     * @param array $command The command to run
     *
     * @throws ProcessFailedException
     */
    private function runProcess(array $command): void
    {
        $process = new Process($command, $this->projectRoot);
        $process->mustRun();
        foreach ($process as $type => $data) {
            if ($type === Process::OUT) {
                echo $data;
            } else {
                echo '<error>'.$data.'</error>';
            }
        }
    }
}
