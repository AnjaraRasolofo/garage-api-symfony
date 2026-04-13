<?php

namespace App\Repository;

use App\Entity\StockMovement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockMovement>
 */
class StockMovementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockMovement::class);
    }

    public function countLowStock(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.quantity < p.alertThreshold')
            ->getQuery()
            ->getSingleScalarResult();
    }

}
