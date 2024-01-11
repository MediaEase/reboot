<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Application;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ApplicationFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function load(ObjectManager $objectManager): void
    {
        $appNames = $this->getAppNames();
        foreach ($appNames as $key => $appName) {
            $application = new Application();
            $application->setName($appName);
            $application->setAltName(str_replace(' ', '-', strtolower($appName)));
            $application->setLogo(str_replace(' ', '-', strtolower($appName)).'.png');
            $types = $this->getTypes();
            $application->setType($types[array_rand($types)]);
            if ($key % 2 === 0) {
                $store = $this->getReference('store-'.str_replace(' ', '-', strtolower($appName)));
                $application->setStore($store);
            }

            // add 1 to 3 groups to each application
            $groups = $this->getReference('group-'.$types[array_rand($types)]);
            $application->addGroup($groups);
            $objectManager->persist($application);
            $this->addReference('application-'.str_replace(' ', '', strtolower($appName)), $application);
            $keyNumber = array_search($appName, $appNames, true);
            $this->addReference('app-'.$keyNumber, $application);
        }

        $objectManager->flush();
    }

    /**
     * @return array<class-string>
     */
    public function getDependencies(): array
    {
        return [
            StoreFixtures::class,
        ];
    }
}
