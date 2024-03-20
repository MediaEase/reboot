<?php

declare(strict_types=1);

namespace App\Updater;

use App\Security\SecretManager;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Handles updating the environment (.env) file.
 *
 * This class is responsible for updating the .env file with new configurations based on the provided form data.
 * It uses the SecretManager to securely manage sensitive information like SMTP credentials and MAILER_DSN.
 */
final class DotenvUpdater
{
    /**
     * Constructor for the DotenvUpdater class.
     *
     * @param SecretManager $secretManager the secret manager service
     */
    public function __construct(private SecretManager $secretManager)
    {
    }

    /**
     * Updates the .env file with provided form data.
     *
     * @param array  $formData    Data to be written to the .env file.
     * @param string $envFilePath Path to the .env file.
     *
     * @throws \Exception if the file does not exist or is not writable
     */
    public function updateEnvFile(array $formData, string $envFilePath): void
    {
        if (!file_exists($envFilePath)) {
            throw new \Exception(sprintf('The file "%s" does not exist.', $envFilePath));
        }

        $dotenv = new Dotenv();
        $envData = $dotenv->parse(file_get_contents($envFilePath), $envFilePath);

        foreach ($formData as $key => $value) {
            if (in_array($key, ['smtp_username', 'smtp_password', 'smtp_hostname', 'smtp_port'], true)) {
                $this->secretManager->setSecret(strtoupper($key), (string) $value);
                continue;
            }

            $formattedKey = strtoupper($key);
            $stringValue = (string) $value;
            $needsQuotes = preg_match('/[^a-zA-Z0-9_]/', $stringValue);
            $formattedValue = $needsQuotes ? sprintf('"%s"', $stringValue) : $stringValue;
            $envData[$formattedKey] = $formattedValue;
        }

        $this->secretManager->setSecret('MAILER_DSN', $this->generateMailerDsn($formData) ?? '');
        $newContent = $this->generateEnvContent($envData);

        try {
            file_put_contents($envFilePath, $newContent);
        } catch (\Exception) {
            throw new \Exception(sprintf('The file "%s" could not be written.', $envFilePath));
        }
    }

    /**
     * Generates the MAILER_DSN string from form data.
     *
     * @param array $formData form data used to generate the MAILER_DSN
     *
     * @return string the generated MAILER_DSN string
     */
    private function generateMailerDsn(array $formData): string
    {
        if (isset($formData['mail_parameters']) && str_starts_with($formData['mail_parameters'], '?')) {
            $formData['mail_parameters'] = substr($formData['mail_parameters'], 1);
        }

        return sprintf(
            'smtp://%s:%s@%s:%s?%s',
            urlencode($formData['smtp_username'] ?? ''),
            urlencode($formData['smtp_password'] ?? ''),
            $formData['smtp_hostname'] ?? '',
            $formData['smtp_port'] ?? '',
            $formData['mail_parameters'] ?? ''
        );
    }

    /**
     * Generates the content for the .env file from the provided data.
     *
     * @param array $envData Data to be included in the .env file.
     *
     * @return string The generated content for the .env file.
     */
    private function generateEnvContent(array $envData): string
    {
        $content = '';
        foreach ($envData as $key => $value) {
            $content .= sprintf('%s=%s%s', $key, $value, PHP_EOL);
        }

        return $content;
    }
}
