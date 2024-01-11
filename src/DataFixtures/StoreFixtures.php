<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Store;
use Doctrine\Persistence\ObjectManager;

final class StoreFixtures extends BaseFixtures
{
    public function load(ObjectManager $objectManager): void
    {
        $appNames = $this->getAppNames();
        $types = $this->getTypes();
        foreach ($appNames as $appName) {
            $store = new Store();
            $store->setName($appName);
            $altName = str_replace(' ', '-', strtolower($appName));
            $store->setAltname($appName);
            $store->setDescription('This is the description for '.$appName);
            $store->setType($types[array_rand($types)]);
            $store->setIsPro(rand(0, 1) === 1);
            $store->setIsAvailable(rand(0, 1) === 1);

            $objectManager->persist($store);

            $this->addReference('store-'.$altName, $store);
        }

        $objectManager->flush();
    }
}
