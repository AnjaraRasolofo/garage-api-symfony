<?php

namespace App\Controller;

use App\Entity\Department;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/departments')]
final class DepartmentController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(DepartmentRepository $repo): JsonResponse
    {
        $departments = $repo->findAll();
        return $this->json($departments, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show($id, DepartmentRepository $repo): JsonResponse
    {
        $department = $repo->find($id);

        if (!$department) {
            return $this->json(['message' => 'Department not found'], 404);
        }

        return $this->json($department, Response::HTTP_OK);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $department = new Department();
        $department->setName($data['name']);
        $department->setCode($data['code'] ?? null);
        $department->setDescription($data['description'] ?? null);

        $em->persist($department);
        $em->flush();

        return $this->json($department, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function edit($id, Request $request, EntityManagerInterface $em, DepartmentRepository $repo): JsonResponse
    {
        $department = $repo->find($id);

        if (!$department) {
            return $this->json(['message' => 'Department not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $department->setName($data['name']);
        $department->setCode($data['code'] ?? null);
        $department->setDescription($data['description'] ?? null);

        $em->flush();

        return $this->json($department, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete($id, DepartmentRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $department = $repo->find($id);

        if (!$department) {
            return $this->json(['message' => 'Department not found'], 404);
        }

        $em->remove($department);
        $em->flush();

        return $this->json(['message' => 'Department deleted']);
    }
}
