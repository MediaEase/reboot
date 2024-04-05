<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Preference;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

final class PreferenceFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $objectManager): void
    {
        foreach (['lucia', 'jason'] as $username) {
            $preference = new Preference();
            $installedApps = $this->getUserInstalledApps($objectManager, $username);
            $randomApps = $installedApps !== []
                ? array_rand($installedApps, rand(1, min(5, count($installedApps))))
                : [];
            if (! is_array($randomApps)) {
                $randomApps = [$randomApps];
            }

            $pinnedApps = array_map(static function ($index) use ($installedApps) {
                return $installedApps[$index];
            }, $randomApps);
            $preference = $this->buildPreference($username, $pinnedApps);
            $objectManager->persist($preference);
        }

        $objectManager->flush();
    }

    /**
     * @return array<class-string>
     */
    public function getDependencies(): array
    {
        return [
            ServiceFixtures::class,
        ];
    }

    /**
     * @return Service[]
     */
    private function getUserInstalledApps(ObjectManager $objectManager, string $username): array
    {
        $objectRepository = $objectManager->getRepository(Service::class);

        return $objectRepository->findInstalledAppsByUser($username);
    }

    private function buildPreference(string $username, ?array $pinnedApps): Preference
    {
        $preference = new Preference();
        $preference->setPinnedApps($pinnedApps);
        $preference->setDisplay($username === 'luciana' ? 'grid' : 'list');
        $preference->setShell($username === 'luciana' ? 'bash' : 'lshell');
        $preference->setUser($this->getReference('user-'.$username));
        $preference->setSelectedWidgets(['cpu_1', 'mem_1', 'disk_1', 'net_3']);
        $preference->setTheme('dark');
        $preference->setBackdrop('user-backdrop.jpg');
        $preference->setAvatar('user-avatar.jpg');
        $preference->setIsFullAppListingEnabled(true);

        return $preference;
    }

    public static function getGroups(): array
    {
        return ['ci'];
    }
}
