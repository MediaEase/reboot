<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\Store;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ApplicationFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function load(ObjectManager $objectManager): void
    {
        $appNames = $this->getAppNames();
        $types = $this->getTypes(); // Ensure this returns store types e.g., ['remote', 'download']

        foreach ($appNames as $appName) {
            $application = new Application();
            $application->setName($appName);
            $application->setAltName(str_replace(' ', '-', strtolower($appName)));
            $application->setLogo(str_replace(' ', '-', strtolower($appName)).'.png');

            $store = new Store();
            $store->setDescription('This is the description for '.$appName);
            $store->setIsPro(rand(0, 1) === 1);
            $store->setIsAvailable(rand(0, 1) === 1);
            $storeType = $types[array_rand($types)]; // Randomly select a store type
            $store->setApplicationType($storeType); // Set the store type
            $store->setApplication($application); // Link the application to the store
            $application->setStore($store);
            $randomGroupName = $types[array_rand($types)];
            $group = $this->getReference('group-'.$randomGroupName);
            $group->addStore($store);

            $objectManager->persist($application);
            $objectManager->persist($store);
            $this->addReference('application-'.str_replace(' ', '', strtolower($appName)), $application);
        }

        $objectManager->flush();
    }

    public function getDependencies(): array
    {
        return [
            GroupFixtures::class,
        ];
    }
}
