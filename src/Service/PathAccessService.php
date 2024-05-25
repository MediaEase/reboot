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

use Psr\Log\LoggerInterface;
use App\Interface\PathAccessInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * PathAccessService checks if a user can access a specific path.
 */
final class PathAccessService implements PathAccessInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * Checks if a user can access the specified path.
     */
    public function userCanAccessPath(string $path, string $username): bool
    {
        try {
            $process = new Process(['sudo', '-u', $username, 'touch', $path.'/.harmony_access_check']);
            $process->mustRun();
            $filesystem = new Filesystem();
            $filesystem->remove($path.'/.harmony_access_check');

            return true;
        } catch (ProcessFailedException $processFailedException) {
            $this->logger->error('Failed to check path ownership', ['exception' => $processFailedException]);

            return false;
        }
    }
}
