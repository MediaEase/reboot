<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Mount;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class MountFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $objectManager): void
    {
        $baseMountPaths = ['/', '/home/{{user}}/', '/home/{{user}}/rclone/'];
        $users = ['lucia', 'jason'];

        foreach ($users as $user) {
            foreach ($baseMountPaths as $baseMountPath) {
                $mountPath = str_replace('{{user}}', $user, $baseMountPath);
                $mount = new Mount();
                $mount->setPath($mountPath);
                $mount->setUser($this->getReference('user-'.$user));
                if (str_contains($mountPath, 'rclone')) {
                    $mount->setIsRclone(true);
                } else {
                    $mount->setIsRclone(false);
                }

                $objectManager->persist($mount);
            }
        }

        $objectManager->flush();
    }

    /**
     * @return array<class-string>
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
