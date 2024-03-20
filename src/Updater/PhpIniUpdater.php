<?php

declare(strict_types=1);

namespace App\Updater;

/**
 * Handles updating the PHP configuration (php.ini) files.
 */
final class PhpIniUpdater
{
    /**
     * Updates php.ini files with provided configuration data.
     *
     * @param array $configData   Data to be written to the php.ini files.
     * @param array $iniFilePaths Paths to the php.ini files.
     *
     * @throws \Exception if a file does not exist or is not writable
     */
    public function updateIniFiles(array $configData, array $iniFilePaths): void
    {
        foreach ($iniFilePaths as $iniFilePath) {
            if (!file_exists($iniFilePath)) {
                throw new \Exception(sprintf('The file "%s" does not exist.', $iniFilePath));
            }

            $iniContent = file_get_contents($iniFilePath);
            foreach ($configData as $key => $value) {
                $iniContent = $this->updateIniValue($iniContent, $key, (string) $value);
            }

            try {
                file_put_contents($iniFilePath, $iniContent);
            } catch (\Exception) {
                throw new \Exception(sprintf('The file "%s" could not be written.', $iniFilePath));
            }
        }
    }

    /**
     * Updates a specific configuration value in the php.ini content.
     *
     * @param string $iniContent The content of the php.ini file.
     * @param string $key        the configuration key
     * @param string $value      the configuration value
     *
     * @return string The updated php.ini content.
     */
    private function updateIniValue(string $iniContent, string $key, string $value): string
    {
        $pattern = sprintf('/^%s\s*=\s*.*/m', preg_quote($key, '/'));
        $replacement = sprintf('%s = %s', $key, $value);

        return preg_replace($pattern, $replacement, $iniContent);
    }

    /**
     * Retrieves the configuration data from a php.ini file.
     *
     * @param string $filePath The path to the php.ini file.
     *
     * @return array the configuration data
     *
     * @throws \Exception if the file does not exist
     */
    public function getIniConfig(string $filePath): array
    {
        $config = [];
        if (!file_exists($filePath)) {
            throw new \Exception(sprintf('The file "%s" does not exist.', $filePath));
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_contains($line, '=') && !str_starts_with($line, ';')) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"");
                $config[$key] = $value;
            }
        }

        return $config;
    }
}
