<?php

namespace App\Controller;

use App\Entity\Repair;
use App\Service\InvoiceService;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class InvoiceController extends AbstractController
{

    #[Route('/repair/{id}/invoice', methods: ['POST'])]
    public function generateInvoice(
        Repair $repair,
        InvoiceService $factory
    ): JsonResponse {
        $invoice = $factory->createFromRepair($repair);

        if ($repair->getInvoice()) {
            throw new \Exception("Facture déjà générée");
        }

        return $this->json([
            'message' => 'Facture générée',
            'invoiceId' => $invoice->getId(),
            'invoiceNumber' => $invoice->getInvoiceNumber()
        ]);
    }

    #[Route('/stats', methods: ['GET'])]
    public function invoiceStats(InvoiceRepository $repo): JsonResponse
    {
        return $this->json([
            'draft' => $repo->countByStatus('draft'),
            'sent' => $repo->countByStatus('sent'),
            'paid' => $repo->countByStatus('paid'),
            'unpaid' => $repo->countByStatus('unpaid'),
        ]);
    }
}
