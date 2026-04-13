<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicle>
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    public function findPaginatedWithSearch(int $page, int $limit, ?string $search): array
    {
        $qb = $this->createQueryBuilder('v')
            ->leftJoin('v.customer', 'c')
            ->addSelect('c');

        // filtre recherche
        if (!empty($search)) {
            $qb->andWhere('
                v.brand LIKE :search OR
                v.model LIKE :search OR
                v.number LIKE :search OR
                c.firstname LIKE :search OR
                c.lastname LIKE :search
            ')
            ->setParameter('search', '%' . $search . '%');
        }

        // total
        $total = (clone $qb)
            ->select('COUNT(v.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // résultats paginés
        $vehicles = $qb
            ->orderBy('v.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [
            'vehicles' => $vehicles,
            'total' => (int) $total
        ];
    }

    public function findByCustomer($customerId)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.customer = :customerId')
            ->setParameter('customerId', $customerId)
            ->getQuery()
            ->getResult();
    }

}
