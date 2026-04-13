<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employee>
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function findPaginatedWithSearch( int $page, int $limit, string $search): array
    {
        $qb = $this->createQueryBuilder('e');

        // Recherche par nom ou poste
        if (!empty($search)) {
            $qb->andWhere('
                e.firstname LIKE :search OR
                e.lastname LIKE :search OR
                e.function LIKE :search
            ')
            ->setParameter('search', '%' . $search . '%');
        }

        // Total filtré
        $totalQb = clone $qb;
        $total = $totalQb
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Data paginée
        $data = $qb
            ->orderBy('e.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [
            'data' => $data,
            'total' => (int) $total
        ];
    }

}
