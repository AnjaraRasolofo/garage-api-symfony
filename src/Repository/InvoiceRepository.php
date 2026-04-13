<?php

namespace App\Repository;

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function search(string $query): array
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.customer', 'c')
            ->leftJoin('c.vehicles', 'v')
            ->select(
                'i.id',
                'i.invoiceNumber',
                'i.total',
                'i.status',
                'c.firstname AS customerFirstName',
                'c.lastname AS customerLastName',
                'v.number AS vehicleNumber'
            )
            ->where('i.invoiceNumber LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->setMaxResults(20)
            ->groupBy('i.id')
            ->getQuery()
            ->getArrayResult();
    }
}
