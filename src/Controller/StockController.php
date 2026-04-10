<?php

namespace App\Controller;

use App\Repository\PartRepository;
use App\Service\StockService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\StockMovementRepository;

#[Route('/api/stock')]
class StockController extends AbstractController
{
    #[Route('/in', methods: ['POST'])]
    public function in(
        Request $request,
        PartRepository $partRepo,
        StockService $stockService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $part = $partRepo->find($data['partId']);

        if (!$part) {
            return $this->json(['error' => 'Pièce non trouvée'], 404);
        }

        $result = $stockService->handleMovement(
            $part,
            'in',
            (float) $data['quantity'],
            $data['reason'] ?? null
        );

        return $this->json($result);
    }

    #[Route('/out', methods: ['POST'])]
    public function out(
        Request $request,
        PartRepository $partRepo,
        StockService $stockService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $part = $partRepo->find($data['partId']);

        if (!$part) {
            return $this->json(['error' => 'Pièce non trouvée'], 404);
        }

        $result = $stockService->handleMovement(
            $part,
            'out',
            (float) $data['quantity'],
            $data['reason'] ?? null
        );

        return $this->json($result);
    }

    #[Route('', methods: ['GET'])]
    public function index(StockMovementRepository $repo): JsonResponse
    {
        $movements = $repo->findBy([], ['id' => 'DESC']);

        $data = array_map(function ($m) {
            return [
                'id' => $m->getId(),
                'type' => $m->getType(),
                'quantity' => $m->getQuantity(),
                'reason' => $m->getReason(),
                'date' => $m->getMovementDate()?->format('Y-m-d H:i'),
                'part' => [
                    'id' => $m->getPart()?->getId(),
                    'name' => $m->getPart()?->getName(),
                    'reference' => $m->getPart()?->getReference()
                ]
            ];
        }, $movements);

        return $this->json($data);
    }

    #[Route('/stats', methods: ['GET'])]
    public function stockStats(StockMovementRepository $repo): JsonResponse
    {
        return $this->json([
            'totalParts' => $repo->count([]),
            'lowStock' => $repo->countLowStock(), // < seuil
            'outOfStock' => $repo->countOutOfStock(),
            'totalValue' => $repo->getTotalStockValue()
        ]);
    }
}
