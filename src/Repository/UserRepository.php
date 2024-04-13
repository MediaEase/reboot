<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(
        PasswordAuthenticatedUserInterface $passwordAuthenticatedUser,
        string $newHashedPassword
    ): void {
        if (! $passwordAuthenticatedUser instanceof User) {
            $message = sprintf('Instances of "%s" are not supported.', $passwordAuthenticatedUser::class);
            throw new UnsupportedUserException($message);
        }

        $passwordAuthenticatedUser->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($passwordAuthenticatedUser);
        $this->getEntityManager()->flush();
    }

    /**
     * Get users with their associated groups, mounts, services, and preferences.
     */
    public function findMyProfile(User $user): ?User
    {
        $query = $this->createQueryBuilder('u')
            ->leftJoin('u.group', 'g')
            ->addSelect('g')
            ->leftJoin('u.mounts', 'm')
            ->addSelect('m')
            ->leftJoin('u.services', 's')
            ->addSelect('s')
            ->leftJoin('u.preferences', 'p')
            ->addSelect('p')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}
