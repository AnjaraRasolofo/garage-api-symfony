<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/employees')]
final class EmployeeController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, EmployeeRepository $repo): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, (int) $request->query->get('limit', 20));
        $search = $request->query->get('search', '');

        $result = $repo->findPaginatedWithSearch( $page, $limit, $search);

        $employees = array_map(fn($c) => [
            'id' => $c->getId(),
            'firstname' => $c->getFirstname(),
            'lastname' => $c->getLastname(),
            'address' => $c->getAddress(),
            'email' => $c->getEmail(),
            'phone' => $c->getPhone(),
            'function' => $c->getJobFunction()
        ], $result['data']);

        return $this->json([
            'data' => $employees,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $result['total'],
                'pages' => ceil($result['total'] / $limit)
            ]
        ], 200, [], ['groups' => 'employee:read']);
    }

    #[Route('/{id}', methods: ['GET'], requirements:['id' => '\d+'])]
    public function show($id, EmployeeRepository $repo): JsonResponse
    {
        try {
            $employee = $repo->find($id);

            if (!$employee) {
                return $this->json([
                    'message' => 'Employee not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return $this->json($employee, Response::HTTP_OK);

        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage() // à enlever en prod si sensible
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/search', methods: ['GET'])]
    public function search(Request $request, EmployeeRepository $repo): JsonResponse
    {
        try {
            $query = trim($request->query->get('query', ''));

            if ($query === '') {
                return $this->json([]);
            }

            $employees = $repo->search($query);

            $data = array_map(function ($e) {
                return [
                    'id' => $e['id'],
                    'firstname' => $e['firstname'],
                    'lastname' => $e['lastname'],
                    'function' => $e['jobFunction'],
                ];
            }, $employees);

            return $this->json($data);

        } catch (\Throwable $e) {
            return $this->json([
                'message' => 'Erreur lors de la recherche des employés',
                'debug' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        DepartmentRepository $deptRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $employee = new Employee();
        $employee->setFirstname($data['firstname']);
        $employee->setLastname($data['lastname'] ?? null);
        $employee->setEmail($data['email'] ?? null);
        $employee->setPhone($data['phone'] ?? null);
        $employee->setAddress($data['address'] ?? null);
        $employee->setJobFunction($data['function'] ?? null);
        $employee->setSalary($data['salary'] ?? null);
        $employee->setStatus($data['status'] ?? null);
        $employee->setNumber($data['number'] ?? null);

        if (!empty($data['hiringDate'])) {
            $employee->setHiringDate(new \DateTime($data['hiringDate']));
        }

        if (!empty($data['birthDate'])) {
            $employee->setBirthDate(new \DateTime($data['birthDate']));
        }

        // Liaison avec le Departement
        if (!empty($data['department_id'])) {
            $department = $deptRepo->find($data['department_id']);
            if ($department) {
                $employee->setDepartment($department);
            }
        }

        $em->persist($employee);
        $em->flush();

        return $this->json($employee, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function edit(
        $id,
        Request $request,
        EntityManagerInterface $em,
        EmployeeRepository $repo,
        DepartmentRepository $deptRepo
    ): JsonResponse {
        $employee = $repo->find($id);

        if (!$employee) {
            return $this->json(['message' => 'Employee not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $employee->setFirstname($data['firstname']);
        $employee->setLastname($data['lastname'] ?? null);
        $employee->setEmail($data['email'] ?? null);
        $employee->setPhone($data['phone'] ?? null);
        $employee->setAddress($data['address'] ?? null);
        $employee->setFunction($data['function'] ?? null);
        $employee->setSalary($data['salary'] ?? null);
        $employee->setStatus($data['status'] ?? null);
        $employee->setNumber($data['number'] ?? null);

        if (!empty($data['hiringDate'])) {
            $employee->setHiringDate(new \DateTime($data['hiringDate']));
        }

        if (!empty($data['birthDate'])) {
            $employee->setBirthDate(new \DateTime($data['birthDate']));
        }

        // update department
        if (!empty($data['department_id'])) {
            $department = $deptRepo->find($data['department_id']);
            if ($department) {
                $employee->setDepartment($department);
            }
        }

        $em->flush();

        return $this->json($employee, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete($id, EmployeeRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $employee = $repo->find($id);

        if (!$employee) {
            return $this->json(['message' => 'Employee not found'], 404);
        }

        $em->remove($employee);
        $em->flush();

        return $this->json(['message' => 'Employee deleted']);
    }
}