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

namespace App\Interface;

/**
 * CommandExecutorInterface executes bash commands.
 */
interface CommandExecutorInterface
{
    /**
     * Executes a bash command.
     *
     * @param string $command   The command to execute
     * @param array  $arguments The arguments to pass to the command
     *
     * @throws ProcessFailedException
     */
    public function execute(string $command, array $arguments = []): void;
}
