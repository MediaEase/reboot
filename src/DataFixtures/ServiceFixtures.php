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

use App\Entity\Service;
use App\Entity\Application;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

final class ServiceFixtures extends BaseFixtures implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $objectManager): void
    {
        $usernames = ['lucia', 'jason'];
        $applications = $objectManager->getRepository(Application::class)->findAll();

        foreach ($usernames as $username) {
            $userAppNames = $this->getUserAppNames($applications);
            $this->processUserAppNames($userAppNames, $username, $objectManager);
        }

        $objectManager->flush();
    }

    private function createService(string $serviceName, string $username, ObjectManager $objectManager, ?Service $parentService = null): Service
    {
        $service = new Service();
        $application = $this->getApplicationByName($objectManager, $serviceName);
        if (!$application instanceof Application) {
            throw new \Exception(sprintf('Application %s not found in the database.', $serviceName));
        }

        $fullServiceName = $this->getFullServiceName($serviceName, $username);
        $this->setupServiceDetails($service, $fullServiceName, $application, $username, $objectManager);

        if ($parentService instanceof Service) {
            $parentService->addChildService($service);
            $objectManager->persist($parentService);
        }

        $objectManager->persist($service);

        return $service;
    }

    /**
     * @param array<Application> $applications
     *
     * @return array<Application>
     */
    private function getUserAppNames(array $applications): array
    {
        shuffle($applications);

        return array_slice($applications, 0, rand(30, 50));
    }

    /**
     * @param array<Application> $userAppNames
     */
    private function processUserAppNames(array $userAppNames, string $username, ObjectManager $objectManager): void
    {
        $installedApps = [];
        foreach ($userAppNames as $application) {
            $userAppName = $application->getName();
            if (! in_array($userAppName, $installedApps, true)) {
                $this->handleUserAppName($userAppName, $username, $installedApps, $objectManager);
                $installedApps[] = $userAppName;
            }
        }
    }

    /**
     * @param array<string> $installedApps
     */
    private function handleUserAppName(
        string $userAppName,
        string $username,
        array &$installedApps,
        ObjectManager $objectManager
    ): void {
        if (in_array($userAppName, $installedApps, true)) {
            return;
        }

        $this->handleSpecialCases($userAppName, $username, $installedApps, $objectManager);
        if (! in_array($userAppName, $installedApps, true)) {
            $this->createService($userAppName, $username, $objectManager);
            $installedApps[] = $userAppName;
        }
    }

    /**
     * @param array<string> $installedApps
     */
    private function handleSpecialCases(
        string $userAppName,
        string $username,
        array &$installedApps,
        ObjectManager $objectManager
    ): void {
        switch ($userAppName) {
            case 'Calibre':
                $this->handleCalibre($username, $objectManager, $installedApps);
                break;
            case 'Deluge':
                $this->handleDeluge($username, $objectManager, $installedApps);
                break;
            case 'Flood':
            case 'ruTorrent':
                $this->handleTorrentClients($userAppName, $username, $objectManager, $installedApps);
                break;
            case 'Rclone':
                $this->handleRclone($username, $objectManager, $installedApps);
                break;
            case 'Fail2ban':
                $this->handleFail2ban($username, $objectManager, $installedApps);
                break;
        }
    }

    /**
     * @param array<string> $installedApps
     */
    private function handleCalibre(string $username, ObjectManager $objectManager, array &$installedApps): void
    {
        $parentService = $this->createService('Calibre-Server', $username, $objectManager);
        $this->createService('Calibre-Web', $username, $objectManager, $parentService);
        $installedApps[] = 'Calibre';
    }

    /**
     * @param array<string> $installedApps
     */
    private function handleDeluge(string $username, ObjectManager $objectManager, array &$installedApps): void
    {
        $parentService = $this->createService('Deluged', $username, $objectManager);
        $this->createService('Deluge-Web', $username, $objectManager, $parentService);
        $installedApps[] = 'Deluge';
    }

    /**
     * @param array<string> $installedApps
     */
    private function handleTorrentClients(
        string $userAppName,
        string $username,
        ObjectManager $objectManager,
        array &$installedApps
    ): void {
        if (in_array('rTorrent', $installedApps, true)) {
            return;
        }

        $parentService = $this->createService('rTorrent', $username, $objectManager);
        $installedApps[] = 'rTorrent';
        $this->createService($userAppName, $username, $objectManager, $parentService);
        $installedApps[] = $userAppName;
    }

    /**
     * @param array<string> $installedApps
     */
    private function handleRclone(string $username, ObjectManager $objectManager, array &$installedApps): void
    {
        if (in_array('Rclone', $installedApps, true)) {
            return;
        }

        $parentService = $this->createService('Rclone', $username, $objectManager);
        $this->createService('Mergerfs', $username, $objectManager, $parentService);
        $installedApps[] = 'Rclone';
    }

    /**
     * @param array<string> $installedApps
     */
    private function handleFail2ban(string $username, ObjectManager $objectManager, array &$installedApps): void
    {
        if (in_array('Fail2ban', $installedApps, true)) {
            return;
        }

        $parentService = $this->createService('Fail2ban', $username, $objectManager);
        $this->createService('Fail2web', $username, $objectManager, $parentService);
        $installedApps[] = 'Fail2ban';
    }

    private function getFullServiceName(string $serviceName, string $username): string
    {
        $serviceName = strtolower(str_replace(' ', '', $serviceName));

        return $serviceName.'@'.$username.'.service';
    }

    private function setupServiceDetails(
        Service $service,
        string $fullServiceName,
        Application $application,
        string $username,
        ObjectManager $objectManager
    ): void {
        $status = ['active', 'inactive', 'disabled'];
        $apiKey = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
        $defaultPort = rand(10000, 30000);
        $sslPort = $defaultPort + 1;
        $ports = ['default' => $defaultPort, 'ssl' => $sslPort];
        strtolower(str_replace(' ', '', $application->getName()));
        $user = $this->getReference('user-'.$username);
        $paths = $this->getPaths($username, $application->getName(), $defaultPort);
        $service->setName($fullServiceName);
        $service->setVersion(rand(1, 10).'.'.rand(0, 10).'.'.rand(0, 50));
        $service->setStatus($status[array_rand($status)]);
        $service->setApiKey($apiKey);
        $service->setPorts([$ports]);
        $service->setApplication($application);
        $service->setUser($user);
        $service->setConfiguration([$paths]);

        $objectManager->persist($service);
    }

    /**
     * @return array<string, string>
     */
    private function getPaths(string $username, string $appReference, int $defaultPort): array
    {
        $appLower = strtolower(str_replace(' ', '', $appReference));
        $subdomainDecision = array_rand([true, false]) !== 0 && (array_rand([true, false]) !== '' && array_rand([true, false]) !== '0') && array_rand([true, false]) !== [];
        $subdomainValue = $subdomainDecision ? sprintf('%s.%s', $username, $appLower) : false;

        return [
            'subdomain' => $subdomainValue,
            'config_path' => '/home/'.$username.'/.config/'.$appLower,
            'database_path' => '/home/'.$username.'/.config/'.$appLower.'/database.db',
            'caddyfile_path' => '/etc/nginx/sites-enabled/'.$username.'.'.$appLower.'.conf',
            'backup_path' => '/home/'.$username.'/.mediaease/backups/'.$appLower,
            'root_url' => 'https://localhost:'.$defaultPort.'/'.$username.'/'.$appLower,
        ];
    }

    private function getApplicationByName(ObjectManager $objectManager, string $serviceName): ?Application
    {
        $application = $objectManager->getRepository(Application::class)->findOneBy(['name' => $serviceName]);

        if (!$application) {
            // Handle special cases where service name might differ from application name
            $specialCases = [
                'Deluged' => 'Deluge',
                'Deluge-Web' => 'Deluge',
                'Rclone' => 'Rclone',
                'Mergerfs' => 'Rclone',
                'Fail2web' => 'Fail2ban',
                'Fail2ban' => 'Fail2ban',
                'Calibre-Web' => 'Calibre',
                'Calibre-Server' => 'Calibre',
            ];

            $application = $objectManager->getRepository(Application::class)->findOneBy(['name' => $specialCases[$serviceName] ?? $serviceName]);
        }

        return $application;
    }

    public function getDependencies(): array
    {
        return [
            GroupFixtures::class,
            UserFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['ci'];
    }
}
