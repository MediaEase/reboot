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

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

abstract class BaseFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * @return array<class-string>
     */
    protected function getAppNames(): array
    {
        return ['AppStore', 'Airsonic', 'Autobrr', 'AutoDL-iRSSi', 'Autoscan',
            'Bazarr', 'Bazarr4K', 'BTSync', 'Calibre', 'Deluge', 'Duplicati', 'Emby Server',
            'Fail2ban', 'FileBot', 'File Browser', 'FlareSolverr', 'Flexget', 'Flood',
            'Headphones', 'Jackett', 'Jellyfin', 'Komga', 'LazyLibrarian', 'Lidarr',
            'Medusa', 'Mylar3', 'Netdata', 'NextCloud', 'Notifiarr', 'noVNC', 'NZBGet', 'NZBHydra2',
            'Ombi', 'OpenVPN', 'Overseerr', 'Plex', 'Prowlarr', 'pyLoad', 'qBittorrent', 'Quassel',
            'Radarr', 'Radarr4K', 'Rapidleech', 'Rclone', 'Readarr', 'Requestrr', 'rTorrent', 'ruTorrent',
            'SABnzbd', 'SeedCross', 'SickChill', 'SickGear', 'Sonarr', 'Sonarr4K', 'Subsonic', 'Syncthing',
            'Tautulli', 'TheLounge', 'Transmission', 'Unpackerr', 'x2Go', 'xTeVe', 'ZNC',
        ];
    }

    public static function getGroups(): array
    {
        return ['prod', 'ci'];
    }

    /**
     * @return array<class-string>
     */
    protected function getTypes(): array
    {
        return ['media', 'download', 'automation', 'remote'];
    }
}
