<?php

namespace App\Controller;

use App\Entity\Repair;
use App\Service\InvoiceService;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/invoices')]
final class InvoiceController extends AbstractController
{

    #[Route('/repair/{id}', methods: ['POST'])]
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

    #[Route('/search', methods: ['GET'])]
    public function search(Request $request, InvoiceRepository $repo): JsonResponse
    {
        try {
            $query = trim($request->query->get('query', ''));

            if ($query === '') {
                return $this->json([]);
            }

            $results = $repo->search($query);

            // si null ou faux retour → sécurisation
            if (!$results) {
                return $this->json([]);
            }

            return $this->json($results);

        } catch (\Throwable $e) {
            return $this->json([
                'message' => 'Erreur lors de la recherche des factures',
                'data' => [] // toujours structure stable pour le front
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
