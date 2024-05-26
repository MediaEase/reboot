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
use App\Interface\CommandExecutorInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * CommandExecutorService executes bash commands.
 *
 * @see CommandExecutorInterface
 * @see Process
 * @see Process::run()
 * @see Process::isSuccessful()
 * @see ProcessFailedException
 */
final class CommandExecutorService implements CommandExecutorInterface
{
    /**
     * Executes a bash command.
     *
     * @param string $command   The command to execute
     * @param array  $arguments The arguments to pass to the command
     *
     * @throws ProcessFailedException
     */
    public function execute(string $command, array $arguments = []): void
    {
        $process = new Process(array_merge([$command], $arguments));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
