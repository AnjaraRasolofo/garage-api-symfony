<?php

namespace App\Repository;

use App\Entity\Part;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Part>
 */
class PartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Part::class);
    }

    public function findPaginatedWithSearch(int $page, int $limit, ?string $search): array
    {
        $qb = $this->createQueryBuilder('p');

        if (!empty($search)) {
            $qb->andWhere('
                p.name LIKE :search OR
                p.reference LIKE :search
            ')
            ->setParameter('search', '%' . $search . '%');
        }

        // Total
        $total = (clone $qb)
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Résultats paginés
        $parts = $qb
            ->orderBy('p.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [
            'data' => $parts,
            'total' => (int) $total
        ];
    }

    public function search(string $query): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.name, p.reference, p.quantity, p.minQuantity')
            ->where('p.name LIKE :q')
            ->orWhere('p.reference LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->setMaxResults(20)
            ->getQuery()
            ->getArrayResult();
    }

    // Rupture de stock
    public function countOutOfStock(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.quantity = 0')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Stock faible 
    public function countLowStock(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.quantity > 0')
            ->andWhere('p.quantity <= p.minQuantity')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Total
    public function countTotalParts(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

}
