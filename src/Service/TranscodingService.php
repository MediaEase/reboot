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

use App\Entity\Service;

/**
 * Service to manage transcoding settings.
 */
final class TranscodingService
{
    /**
     * Enable transcoding for the given service.
     *
     * @param Service $service The service to enable transcoding for
     */
    public function enableTranscoding(Service $service): void
    {
        $configFile = $this->getConfigFilePath($service);
        if ($configFile !== null) {
            $keys = $this->getTranscodingKeys($service);
            $this->updateConfigFile($configFile, $keys, $service->getApplication()->getName());
        }
    }

    /**
     * Disable transcoding for the given service.
     *
     * @param Service $service The service to disable transcoding for
     */
    public function disableTranscoding(Service $service): void
    {
        $configFile = $this->getConfigFilePath($service);
        if ($configFile !== null) {
            $keys = $this->getTranscodingKeys($service);
            $keys = $this->invertKeys($keys);
            $this->updateConfigFile($configFile, $keys, $service->getApplication()->getName());
        }
    }

    /**
     * Get the configuration file path based on the service application name.
     *
     * @param Service $service The service to get the configuration file path for
     *
     * @return string|null The configuration file path or null if not found
     */
    private function getConfigFilePath(Service $service): ?string
    {
        $configPath = $service->getConfiguration()[0]['config_path'];

        return match ($service->getApplication()->getName()) {
            'Plex' => $configPath.'/PlexMediaServer/Library/Application Support/Plex Media Server/Preferences.xml',
            'Emby' => $configPath.'/emby-server/config/system.xml',
            'Jellyfin' => $configPath.'/jellyfin/config/system.xml',
            default => null,
        };
    }

    /**
     * Get the transcoding keys based on the service application name.
     *
     * @param Service $service The service to get the transcoding keys for
     *
     * @return array The transcoding keys
     */
    private function getTranscodingKeys(Service $service): array
    {
        return match ($service->getApplication()->getName()) {
            'Plex' => $this->getPlexTranscodingKeys(),
            'Emby' => $this->getEmbyTranscodingKeys(),
            'Jellyfin' => $this->getJellyfinTranscodingKeys(),
            default => [],
        };
    }

    /**
     * Get the transcoding keys for Plex.
     *
     * @return array The transcoding keys for Plex
     */
    private function getPlexTranscodingKeys(): array
    {
        return [
            'HardwareAcceleratedCodecs' => '1',
            'HardwareAcceleratedEncoders' => '1',
            'TranscoderCanOnlyRemuxVideo' => '0',
            'TranscoderDefaultDuration' => '120',
            'TranscoderH264OptionsOverride' => 'veryfast',
            'TranscoderTempDirectory' => '/dev/shm',
            'TranscoderThrottleBuffer' => '120',
            'TranscoderToneMapping' => '1',
        ];
    }

    /**
     * Get the transcoding keys for Emby.
     *
     * @return array The transcoding keys for Emby
     */
    private function getEmbyTranscodingKeys(): array
    {
        return [
            'HardwareAccelerationMode' => '1',
            'EnableHardwareToneMapping' => 'true',
            'EnableSoftwareToneMapping' => 'true',
            'EnableHardwareEncoding' => 'true',
            'TranscodingTempPath' => '/dev/shm',
            'H264preset' => 'veryfast',
        ];
    }

    /**
     * Get the transcoding keys for Jellyfin.
     *
     * @return array The transcoding keys for Jellyfin
     */
    private function getJellyfinTranscodingKeys(): array
    {
        return [
            'HardwareAccelerationMode' => '1',
            'EnableHardwareToneMapping' => 'true',
            'EnableSoftwareToneMapping' => 'true',
            'EnableHardwareEncoding' => 'true',
            'TranscodingTempPath' => '/dev/shm',
            'H264preset' => 'veryfast',
        ];
    }

    /**
     * Update the configuration file with the given keys.
     *
     * @param string $configFile      The path to the configuration file
     * @param array  $keys            The transcoding keys to update
     * @param string $applicationName The name of the application
     */
    private function updateConfigFile(string $configFile, array $keys, string $applicationName): void
    {
        if (!file_exists($configFile)) {
            throw new \RuntimeException(sprintf('Configuration file %s does not exist.', $configFile));
        }

        if ($applicationName === 'Plex') {
            $this->updatePlexConfigFile($configFile, $keys);
        } else {
            $this->updateXmlConfigFile($configFile, $keys);
        }
    }

    /**
     * Update the Plex configuration file with the given keys.
     *
     * @param string $configFile The path to the configuration file
     * @param array  $keys       The transcoding keys to update
     */
    private function updatePlexConfigFile(string $configFile, array $keys): void
    {
        $xml = simplexml_load_file($configFile);

        foreach ($keys as $key => $value) {
            if (isset($xml[$key])) {
                $xml[$key] = $value;
            } else {
                $xml->addAttribute($key, $value);
            }
        }

        $xml->asXML($configFile);
    }

    /**
     * Update the XML configuration file with the given keys.
     *
     * @param string $configFile The path to the configuration file
     * @param array  $keys       The transcoding keys to update
     */
    private function updateXmlConfigFile(string $configFile, array $keys): void
    {
        $xml = simplexml_load_file($configFile);

        foreach ($keys as $key => $value) {
            if (isset($xml->$key)) {
                $xml->$key = $value;
            } else {
                $xml->addChild($key, $value);
            }
        }

        $xml->asXML($configFile);
    }

    /**
     * Invert the transcoding keys to disable transcoding.
     *
     * @param array $keys The transcoding keys to invert
     *
     * @return array The inverted transcoding keys
     */
    private function invertKeys(array $keys): array
    {
        $inversionMap = [
            '1' => '0',
            '0' => '1',
            'true' => 'false',
            'false' => 'true',
        ];

        return array_map(static fn($value) => $inversionMap[$value] ?? $value, $keys);
    }
}
