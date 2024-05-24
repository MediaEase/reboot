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

use App\Entity\Application;
use App\Entity\Store;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

final class ApplicationFixtures extends BaseFixtures implements DependentFixtureInterface, FixtureGroupInterface
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
            $storeType = $types[array_rand($types)];
            $store->setType($storeType);
            $store->setApplication($application);
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

    public static function getGroups(): array
    {
        return ['prod', 'ci'];
    }
}
