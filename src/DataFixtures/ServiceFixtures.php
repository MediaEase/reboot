<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

final class ServiceFixtures extends BaseFixtures implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $objectManager): void
    {
        $usernames = ['lucia', 'jason'];
        $appNames = $this->getAppNames();

        foreach ($usernames as $username) {
            $userAppNames = $this->getUserAppNames($appNames);
            $this->processUserAppNames($userAppNames, $username, $objectManager);
        }

        $objectManager->flush();
    }

    /**
     * @return array<class-string>
     */
    public function getDependencies(): array
    {
        return [
            ApplicationFixtures::class,
        ];
    }

    // private function createService(string $serviceName, string $username, ObjectManager $objectManager): void
    // {
    //     $service = new Service();
    //     $appReference = $this->getAppReference($serviceName);
    //     $fullServiceName = $this->getFullServiceName($serviceName, $username);
    //     $this->setupServiceDetails($service, $fullServiceName, $appReference, $username, $objectManager);
    // }

    private function createService(string $serviceName, string $username, ObjectManager $objectManager, ?Service $parentService = null): Service
    {
        $service = new Service();
        $appReference = $this->getAppReference($serviceName);
        $fullServiceName = $this->getFullServiceName($serviceName, $username);
        $this->setupServiceDetails($service, $fullServiceName, $appReference, $username, $objectManager);

        if ($parentService instanceof Service) {
            $parentService->addChildService($service);
            $objectManager->persist($parentService);
        }

        $objectManager->persist($service);

        return $service;
    }

    /**
     * @param array<class-string> $appNames
     *
     * @return array<class-string>
     */
    private function getUserAppNames(array $appNames): array
    {
        shuffle($appNames);

        return array_slice($appNames, 0, rand(30, 50));
    }

    /**
     * @param array<class-string> $userAppNames
     */
    private function processUserAppNames(array $userAppNames, string $username, ObjectManager $objectManager): void
    {
        $installedApps = [];
        foreach ($userAppNames as $userAppName) {
            if (! in_array($userAppName, $installedApps, true)) {
                $this->handleUserAppName($userAppName, $username, $installedApps, $objectManager);
                $installedApps[] = $userAppName;
            }
        }
    }

    /**
     * @param array<class-string> $installedApps
     */
    private function handleUserAppName(
        string $userAppName,
        string $username,
        array &$installedApps,
        ObjectManager $objectManager
    ): void {
        $appReference = $this->getAppReference($userAppName);
        if (in_array($appReference, $installedApps, true)) {
            return;
        }

        $this->handleSpecialCases($userAppName, $username, $installedApps, $objectManager);
        if (! in_array($appReference, $installedApps, true)) {
            $this->createService($userAppName, $username, $objectManager);
            $installedApps[] = $appReference;
        }
    }

    /**
     * @param array<class-string> $installedApps
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
     * @param array<class-string> $installedApps
     */
    private function handleCalibre(string $username, ObjectManager $objectManager, array &$installedApps): void
    {
        $parentService = $this->createService('Calibre-Server', $username, $objectManager);
        $this->createService('Calibre-Web', $username, $objectManager, $parentService);
        $installedApps[] = 'Calibre';
    }

    /**
     * @param array<class-string> $installedApps
     */
    private function handleDeluge(string $username, ObjectManager $objectManager, array &$installedApps): void
    {
        $parentService = $this->createService('Deluged', $username, $objectManager);
        $this->createService('Deluge-Web', $username, $objectManager, $parentService);
        $installedApps[] = 'Deluge';
    }

    /**
     * @param array<class-string> $installedApps
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
     * @param array<class-string> $installedApps
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
     * @param array<class-string> $installedApps
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
        string $appReference,
        string $username,
        ObjectManager $objectManager
    ): void {
        $status = ['active', 'inactive', 'disabled'];
        $apiKey = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
        $defaultPort = rand(10000, 30000);
        $sslPort = $defaultPort + 1;
        $ports = ['default' => $defaultPort, 'ssl' => $sslPort];
        $appLower = strtolower(str_replace(' ', '', $appReference));
        $application = $this->getReference('application-'.$appLower);
        $user = $this->getReference('user-'.$username);
        $paths = $this->getPaths($username, $appReference, $defaultPort);
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

    private function getAppReference(string $serviceName): string
    {
        if (in_array($serviceName, ['Deluged', 'Deluge-Web'], true)) {
            return 'Deluge';
        }

        if (in_array($serviceName, ['Rclone', 'Mergerfs'], true)) {
            return 'Rclone';
        }

        if (in_array($serviceName, ['Fail2web', 'Fail2ban'], true)) {
            return 'Fail2ban';
        }

        if (in_array($serviceName, ['Calibre-Web', 'Calibre-Server'], true)) {
            return 'Calibre';
        }

        return $serviceName;
    }

    public static function getGroups(): array
    {
        return ['ci'];
    }
}
