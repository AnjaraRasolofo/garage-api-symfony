<?php

namespace App\Controller;

use App\Entity\WorkTaskTemplate;
use App\Repository\WorkTaskTemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/work-task-templates')]
final class WorkTaskTemplateController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(WorkTaskTemplateRepository $repo): JsonResponse
    {
        $templates = $repo->findAll();

        $data = array_map(fn($t) => [
            'id' => $t->getId(),
            'title' => $t->getTitle(),
            'description' => $t->getDescription(),
            'defaultLaborCost' => $t->getDefaultLaborCost(),
        ], $templates);

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/list', methods: ['GET'])]
    public function list(WorkTaskTemplateRepository $repo): JsonResponse
    {
        $templates = $repo->findAll();

        $data = array_map(function ($t) {
            return [
                'id' => $t->getId(),
                'title' => $t->getTitle(),
                'defaultLaborCost' => $t->getDefaultLaborCost()
            ];
        }, $templates);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show($id, WorkTaskTemplateRepository $repo): JsonResponse
    {
        $template = $repo->find($id);

        if (!$template) {
            return $this->json(['message' => 'WorkTaskTemplate not found'], 404);
        }

        $data = [
            'id' => $template->getId(),
            'title' => $template->getTitle(),
            'description' => $template->getDescription(),
            'defaultLaborCost' => $template->getDefaultLaborCost(),
        ];

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $template = new WorkTaskTemplate();
        $template->setTitle($data['title']);
        $template->setDescription($data['description'] ?? null);
        $template->setDefaultLaborCost($data['defaultLaborCost'] ?? 0);

        $em->persist($template);
        $em->flush();

        $response = [
            'id' => $template->getId(),
            'title' => $template->getTitle(),
            'description' => $template->getDescription(),
            'defaultLaborCost' => $template->getDefaultLaborCost(),
        ];

        return $this->json($response, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function edit($id, Request $request, EntityManagerInterface $em, WorkTaskTemplateRepository $repo): JsonResponse
    {
        $template = $repo->find($id);

        if (!$template) {
            return $this->json(['message' => 'WorkTaskTemplate not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $template->setTitle($data['title']);
        $template->setDescription($data['description'] ?? null);
        $template->setDefaultLaborCost($data['defaultLaborCost'] ?? 0);

        $em->flush();

        $response = [
            'id' => $template->getId(),
            'title' => $template->getTitle(),
            'description' => $template->getDescription(),
            'defaultLaborCost' => $template->getDefaultLaborCost(),
        ];

        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete($id, WorkTaskTemplateRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $template = $repo->find($id);

        if (!$template) {
            return $this->json(['message' => 'WorkTaskTemplate not found'], 404);
        }

        $em->remove($template);
        $em->flush();

        return $this->json(['message' => 'WorkTaskTemplate deleted']);
    }
}
