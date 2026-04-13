<?php

namespace App\Controller;

use App\Entity\Part;
use App\Repository\PartRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/parts')]
final class PartController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, PartRepository $repo): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
            $limit = max(1, (int) $request->query->get('limit', 10));
            $search = $request->query->get('search', '');

            $result = $repo->findPaginatedWithSearch($page, $limit, $search);

            $parts = $result['data'];
            $total = $result['total'];

            $data = array_map(fn($p) => [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'reference' => $p->getReference(),
                'price' => $p->getPrice(),
                'quantity' => $p->getQuantity(),
                'provider' => $p->getProvider()
            ], $parts);

            return $this->json([
                'data' => $data,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
        ]);

        
    }

    #[Route('/list', methods: ['GET'])]
    public function list(PartRepository $repo): JsonResponse
    {
        $parts = $repo->findAll();

        $data = array_map(function ($p) {
            return [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'reference' => $p->getReference(),
                'price' => $p->getPrice(),
            ];
        }, $parts);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/search', methods: ['GET'])]
    public function search(Request $request, PartRepository $repo): JsonResponse
    {
        $query = trim($request->query->get('query', ''));

        if ($query === '') {
            return $this->json([]);
        }

        $parts = $repo->search($query);

        return $this->json($parts);
    }

    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show($id, PartRepository $repo): JsonResponse
    {
        $part = $repo->find($id);

        if (!$part) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $part->getId(),
            'name' => $part->getName(),
            'reference' => $part->getReference(),
            'description' => $part->getDescription(),
            'price' => $part->getPrice(),
            'provider' => $part->getProvider(),
            'quantity' => $part->getQuantity(),
            'minQuantity' => $part->getMinQuantity(),
            'category' => $part->getCategory() ? [
                'id' => $part->getCategory()->getId(),
                'name' => $part->getCategory()->getName(),
            ] : null,
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $catRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $part = new Part();
        $part->setName($data['name']);
        $part->setReference($data['reference']);
        $part->setDescription($data['description'] ?? null);
        $part->setQuantity($data['quantity'] ?? 0);
        $part->setMinQuantity($data['minQuantity'] ?? 0);
        $part->setProvider($data['provider']);

        if (!empty($data['categoryId'])) {
            $category = $catRepo->find($data['categoryId']);
            if ($category) {
                $part->setCategory($category);
            }
        }

        $em->persist($part);
        $em->flush();

        return $this->json([
            'id' => $part->getId(),
            'name' => $part->getName(),
            'reference' => $part->getReference(),
            'description' => $part->getDescription(),
            'quantity' => $part->getQuantity(),
            'minQuantity' => $part->getMinQuantity(),
            'provider' => $part->getProvider(),
            'category' => [
                'id' => $part->getCategory()?->getId(),
                'name' => $part->getCategory()?->getName()
            ]
        ], 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function edit(
        $id,
        Request $request,
        EntityManagerInterface $em,
        PartRepository $repo,
        CategoryRepository $catRepo
    ): JsonResponse {
        $part = $repo->find($id);

        if (!$part) {
            return $this->json(['message' => 'Part not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $part->setName($data['name']);
        $part->setReference($data['reference']);
        $part->setDescription($data['description'] ?? null);
        $part->setQuantity($data['quantity'] ?? 0);
        $part->setMinQuantity($data['minQuantity'] ?? 0);
        $part->setProvider($data['provider']);

        if (!empty($data['categoryId'])) {
            $category = $catRepo->find($data['categoryId']);
            if ($category) {
                $part->setCategory($category);
            }
        }

        $em->flush();

        return $this->json([
            'id' => $part->getId(),
            'name' => $part->getName(),
            'reference' => $part->getReference(),
            'description' => $part->getDescription(),
            'quantity' => $part->getQuantity(),
            'minQuantity' => $part->getMinQuantity(),
            'provider' => $part->getProvider(),
            'category' => [
                'id' => $part->getCategory()?->getId(),
                'name' => $part->getCategory()?->getName()
            ]
        ], 201);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete($id, PartRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $part = $repo->find($id);

        if (!$part) {
            return $this->json(['message' => 'Part not found'], 404);
        }

        $em->remove($part);
        $em->flush();

        return $this->json(['message' => 'Part deleted']);
    }
}