<?php

namespace App\Service;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Entity\Repair;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    // invoice created from Repair - Création de facture à partir de la réparation
    public function createFromRepair(Repair $repair): Invoice
    {
        $invoice = new Invoice();

        $invoice->setRepair($repair);
        $invoice->setCustomer($repair->getVehicle()->getCustomer());
        $invoice->setInvoiceDate(new \DateTime());

        // numéro facture simple
        $invoice->setInvoiceNumber('INV-' . time());

        $total = 0;

        $invoice->setRepair($repair);
        $repair->setInvoice($invoice);

        foreach ($repair->getRepairLines() as $repairItem) {
            $item = new InvoiceItem();
            $item->setInvoice($invoice);
            $item->setDescription($repairItem->getCustomTitle());
            //$item->setQuantity($repairItem->get());
            $item->setUnitPrice($repairItem->getTotal());

            //$lineTotal = $repairItem->getQuantity() * $repairItem->getPrice();
            $item->setTotal($repairItem->getTotal());

            //$total += $lineTotal;

            $this->em->persist($item);
        }

        $invoice->setTotal($total);

        $this->em->persist($invoice);
        $this->em->flush();

        return $invoice;
    }

    // invoice created from parts - Création de la facture à partir d'une vente directe de pièces de rechange
}