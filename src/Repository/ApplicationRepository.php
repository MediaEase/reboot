<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Application>
 *
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Application::class);
    }

    /**
     * Get applications with their associated stores.
     *
     * @return Application[]
     */
    public function findApplicationsWithStores(): array
    {
        $query = $this->createQueryBuilder('a')
            ->leftJoin('a.store', 's')
            ->addSelect('s')
            ->getQuery();

        return $query->getResult();
    }
}
