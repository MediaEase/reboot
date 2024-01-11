<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Service;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 *
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Service::class);
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function findInstalledAppsByUser(string $username): array
    {
        $result = $this->createQueryBuilder('s')
            ->innerJoin('s.application', 'a')
            ->innerJoin('s.user', 'u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->select('a.altname as name, s.configuration as configuration')
            ->distinct(true)
            ->getQuery()
            ->getResult();
        assert(is_array($result));

        return $result;
    }

    public function findServiceForUser(User $user, int $serviceId): ?Service
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :serviceId')
            ->andWhere('s.user = :user')
            ->setParameter('serviceId', $serviceId)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Service[] Returns an array of Service objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Service
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
