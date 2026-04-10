<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categories')]
final class CategoryController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(CategoryRepository $repo): JsonResponse
    {
        $categories = $repo->findAll();
        
        $data = array_map(fn($p) => [
            'id' => $p->getId(),
            'name' => $p->getName(),
            'description' => $p->getDescription(),
        ], $categories);

        return $this->json($categories, Response::HTTP_OK);

    }

    #[Route('/list', methods: ['GET'])]
    public function list(CategoryRepository $repo): JsonResponse
    {
        $categories = $repo->findAll();

        $data = array_map(function ($p) {
            return [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'description' => $p->getDescription()
            ];
        }, $categories);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show($id, CategoryRepository $repo): JsonResponse
    {
        $category = $repo->find($id);

        if (!$category) {
            return $this->json(['message' => 'Category not found'], 404);
        }

        $data = [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription()
        ];

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $category = new Category();
        $category->setName($data['name']);
        $category->setDescription($data['description'] ?? null);

        $em->persist($category);
        $em->flush();

        $data = [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription()
        ];

        return $this->json($data, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function edit($id, Request $request, EntityManagerInterface $em, CategoryRepository $repo): JsonResponse
    {
        $category = $repo->find($id);

        if (!$category) {
            return $this->json(['message' => 'Category not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $category->setName($data['name']);
        $category->setDescription($data['description'] ?? null);

        $em->flush();

        $data = [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription()
        ];

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete($id, CategoryRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $category = $repo->find($id);

        if (!$category) {
            return $this->json(['message' => 'Category not found'], 404);
        }

        $em->remove($category);
        $em->flush();

        return $this->json(['message' => 'Category deleted']);
    }
}
