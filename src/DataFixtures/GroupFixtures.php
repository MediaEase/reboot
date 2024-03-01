<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

final class GroupFixtures extends Fixture implements FixtureGroupInterface
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

    public static function getGroups(): array
    {
        return ['prod', 'ci'];
    }
}
