<?php

namespace App\Controller;

use App\Repository\PartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/stock', methods: ['GET'])]
    public function stockStats(PartRepository $repo): JsonResponse
    {
        try {
            $lowStock = $repo->countLowStock();
            $outOfStock = $repo->countOutOfStock();
            $total = $repo->countTotalParts();

            return $this->json([
                'outOfStock' => $outOfStock,
                'lowStock' => $lowStock,
                'total' => $total
            ]);
        
        } catch (\Throwable $e) {
        return $this->json([
            'error' => 'Erreur serveur',
            'message' => $e->getMessage()
        ], 500);
         
        }
        
    }
}
