<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Manage secrets using the Symfony secrets command.
 *
 * This class allows you to set and retrieve secrets using the Symfony secrets command.
 */
final class SecretManager
{
    /**
     * Set a secret using the Symfony secrets command.
     *
     * @param string $key   the name of the secret
     * @param string $value the value of the secret
     *
     * @throws ProcessFailedException if the command fails
     */
    public function setSecret(string $key, string $value): void
    {
        // change current path to the project root
        $projectRootPath = dirname(__DIR__, 2);
        $process = new Process([
            'symfony', 'console', 'secrets:set', $key, $value, '--local',
        ]);
        $process->setWorkingDirectory($projectRootPath);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    /**
     * Retrieve a specific secret's value.
     *
     * @param string $key the name of the secret
     *
     * @return string|null the secret value or null if not found
     */
    public function getSecret(string $key): ?string
    {
        // change current path to the project root
        $projectRootPath = dirname(__DIR__, 2);
        $process = new Process([
            'symfony', 'console', 'secrets:list', '--reveal',
        ]);
        $process->setWorkingDirectory($projectRootPath);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this->parseSecretValue($process->getOutput(), $key);
    }

    /**
     * Parse the output of the secrets:list command to find the secret.
     *
     * @param string $output the output of the secrets:list command
     * @param string $key    the name of the secret to look for
     *
     * @return string|null the secret value or null if not found
     */
    private function parseSecretValue(string $output, string $key): ?string
    {
        $lines = explode("\n", $output);
        foreach ($lines as $line) {
            if (str_contains($line, $key)) {
                // Extract the local value if available, otherwise the normal value
                preg_match('/\s+(?:[^\s]+)\s+(?:"([^"]*)"|([^ ]+))\s+(?:"([^"]*)"|([^ ]+)|)/', $line, $matches);

                return isset($matches[3]) ? $matches[3] : (isset($matches[1]) ? $matches[1] : null);
            }
        }

        return null;
    }
}
