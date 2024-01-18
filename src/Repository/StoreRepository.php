<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Store;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Store>
 *
 * @method Store|null find($id, $lockMode = null, $lockVersion = null)
 * @method Store|null findOneBy(array $criteria, array $orderBy = null)
 * @method Store[]    findAll()
 * @method Store[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class StoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Store::class);
    }

    public function findStoresAccessibleByUser(User $user): array
    {
        $queryBuilder = $this->createQueryBuilder('store')
            ->join('store.instances', 'instances')
            ->join('instances.group', 'group')
            ->where('group = :group')
            ->setParameter('group', $user->getUsername());

        return $queryBuilder->getQuery()->getResult();
    }
}
