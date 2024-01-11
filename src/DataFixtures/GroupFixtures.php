<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class GroupFixtures extends Fixture
{
    public function load(ObjectManager $objectManager): void
    {
        $groupNames = ['media', 'automation', 'download', 'full', 'remote'];

        foreach ($groupNames as $groupName) {
            $group = new Group();
            $group->setName($groupName);
            $this->addReference('group-'.$groupName, $group);
            $objectManager->persist($group);
        }

        $objectManager->flush();
    }
}
