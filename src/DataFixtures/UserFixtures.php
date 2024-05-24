<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

final class UserFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function load(ObjectManager $objectManager): void
    {
        $usernames = ['lucia', 'jason'];
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];

        foreach ($usernames as $key => $username) {
            $this->createUser($username, $roles[$key], $objectManager);
        }

        $objectManager->flush();
    }

    /**
     * @return array<class-string>
     */
    public function getDependencies(): array
    {
        return [
            GroupFixtures::class,
        ];
    }

    private function createUser(string $username, string $role, ObjectManager $objectManager): void
    {
        $user = new User();
        $group = $this->getReference('group-media');
        $isVerified = (bool)random_int(0, 1);
        if ($isVerified) {
            $user->setActivatedAt(new \DateTimeImmutable());
        }

        $user->setUsername($username)
            ->setRoles([$role])
            ->setGroup($group)
            ->setEmail($username.'@example.com')
            ->setIsVerified($isVerified)
            ->setPassword($this->userPasswordHasher->hashPassword($user, 'password'))
            ->setApiKey(bin2hex(random_bytes(16)))
            ->setRegisteredAt(new \DateTimeImmutable());

        $objectManager->persist($user);
        $this->addReference('user-'.$username, $user);
    }

    public static function getGroups(): array
    {
        return ['ci'];
    }
}
