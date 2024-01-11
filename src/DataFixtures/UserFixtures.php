<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture implements DependentFixtureInterface
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

        $user->setUsername($username)
            ->setRoles([$role])
            ->setAppGroup($group)
            ->setEmail($username.'@example.com')
            ->setIsVerified(true)
            ->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));

        $objectManager->persist($user);
        $this->addReference('user-'.$username, $user);
    }
}
