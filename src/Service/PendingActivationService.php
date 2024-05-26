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

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

final class PendingActivationService
{
    public function __construct(
        private string $projectDir,
        private Filesystem $filesystem,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Create a temporary file with the plain password.
     *
     * @param User   $user          The user to register
     * @param string $plainPassword The plain password
     */
    public function create(User $user, string $plainPassword): void
    {
        $filePath = $this->projectDir.'/var/'.$user->getUsername();
        try {
            $this->filesystem->dumpFile($filePath, $plainPassword);
        } catch (IOException $ioException) {
            $this->logger->error('Failed to create temporary password file', [
                'exception' => $ioException,
                'filePath' => $filePath,
                'username' => $user->getUsername(),
            ]);
            throw $ioException;
        }
    }

    /**
     * Read the temporary password file.
     *
     * @param User $user The user to register
     *
     * @return string|null The plain password or null if the file does not exist
     */
    public function read(User $user): ?string
    {
        $filePath = $this->projectDir.'/var/'.$user->getUsername();
        try {
            if (!$this->filesystem->exists($filePath)) {
                throw new FileNotFoundException(sprintf('File "%s" not found.', $filePath));
            }

            return file_get_contents($filePath);
        } catch (IOException $ioException) {
            $this->logger->error('Failed to read temporary password file', [
                'exception' => $ioException,
                'filePath' => $filePath,
                'username' => $user->getUsername(),
            ]);
            throw $ioException;
        }
    }

    /**
     * Delete the temporary password file.
     *
     * @param User $user The user to register
     */
    public function delete(User $user): void
    {
        $filePath = $this->projectDir.'/var/'.$user->getUsername();
        try {
            $this->filesystem->remove($filePath);
        } catch (IOException $ioException) {
            $this->logger->error('Failed to delete temporary password file', [
                'exception' => $ioException,
                'filePath' => $filePath,
                'username' => $user->getUsername(),
            ]);
            throw $ioException;
        }
    }
}
