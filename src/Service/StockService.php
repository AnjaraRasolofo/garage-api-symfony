<?php

namespace App\Service;

use App\Entity\Part;
use App\Entity\StockMovement;
use Doctrine\ORM\EntityManagerInterface;

class StockService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function handleMovement(Part $part, string $type, float $quantity, ?string $reason = null): array
    {
        if ($quantity <= 0) {
            return ['error' => 'Quantité invalide'];
        }

        if ($type === 'out' && $part->getQuantity() < $quantity) {
            return ['error' => 'Stock insuffisant'];
        }

        // update stock
        if ($type === 'in') {
            $part->setQuantity($part->getQuantity() + $quantity);
        } else {
            $part->setQuantity($part->getQuantity() - $quantity);
        }

        // create movement
        $movement = new StockMovement();
        $movement->setPart($part);
        $movement->setType($type);
        $movement->setQuantity($quantity);
        $movement->setReason($reason);
        $movement->setMovementDate(new \DateTime());

        $this->em->persist($movement);
        $this->em->persist($part);
        $this->em->flush();

        return [
            'message' => $type === 'in'
                ? 'Entrée de stock enregistrée'
                : 'Sortie de stock enregistrée',
            'part' => [
                'id' => $part->getId(),
                'name' => $part->getName(),
                'quantity' => $part->getQuantity(),
                'category' => [
                    'id' => $part->getCategory()?->getId(),
                    'name' => $part->getCategory()?->getName()
                ]
            ]
        ];
    }
}