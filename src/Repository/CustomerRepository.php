<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findPaginatedWithSearch(int $page, int $limit, ?string $search): array
{
    $qb = $this->createQueryBuilder('c');

    if (!empty($search)) {
        $qb->andWhere('
            c.firstname LIKE :search OR
            c.lastname LIKE :search OR
            c.type LIKE :search
        ')
        ->setParameter('search', '%' . $search . '%');
    }

    // Total
    $total = (clone $qb)
        ->select('COUNT(c.id)')
        ->getQuery()
        ->getSingleScalarResult();

    // Résultats
    $customers = $qb
        ->orderBy('c.id', 'DESC')
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();

    return [
        'data' => $customers,
        'total' => (int) $total
    ];
}

    //    /**
    //     * @return Customer[] Returns an array of Customer objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Customer
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
