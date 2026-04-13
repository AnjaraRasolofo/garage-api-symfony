<?php

namespace App\Repository;

use App\Entity\Repair;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Repair>
 */
class RepairRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Repair::class);
    }

    public function findPaginatedWithSearch(int $page, int $limit, ?string $search): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.vehicle', 'v')
            ->leftJoin('v.customer', 'c')
            ->addSelect('v', 'c');

        if ($search) {
            $qb->andWhere('
                c.name LIKE :search OR 
                v.name LIKE :search OR 
                r.status LIKE :search
            ')
            ->setParameter('search', '%' . $search . '%');
        }

        $total = (clone $qb)
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $data = $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();

        return [
            'data' => $data,
            'total' => $total
        ];
    }

}
