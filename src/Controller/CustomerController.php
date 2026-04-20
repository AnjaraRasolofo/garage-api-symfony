<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/customers')]
final class CustomerController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, CustomerRepository $repo): JsonResponse
    {
        try {
            $page = max(1, (int) $request->query->get('page', 1));
            $limit = max(1, (int) $request->query->get('limit', 20));
            $search = $request->query->get('search', '');

            $result = $repo->findPaginatedWithSearch($page, $limit, $search);

            $customers = array_map(function ($c) {
                return [
                    'id' => $c->getId(),
                    'firstname' => $c->getFirstname(),
                    'lastname' => $c->getLastname(),
                    'email' => $c->getEmail(),
                    'phone' => $c->getPhone(),
                    'type' => $c->getType()
                ];
            }, $result['data'] ?? []);

            $total = (int) ($result['total'] ?? 0);

            return $this->json([
                'data' => $customers,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => $limit > 0 ? ceil($total / $limit) : 1
                ]
            ], 200);

        } catch (\Throwable $e) {
            return $this->json([
                'error' => 'Erreur serveur',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show($id, CustomerRepository $repo): JsonResponse
    {
        $customer = $repo->find($id);

        if (!$customer) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $vehicles = [];
        $invoices = [];

        foreach ($customer->getVehicles() as $vehicle) {
            $vehicles[] = [
                'id' => $vehicle->getId(),
                'brand' => $vehicle->getBrand(),
                'model' => $vehicle->getModel(),
                'number' => $vehicle->getNumber(),
            ];
        }

        foreach ($customer->getInvoices() as $invoice) {
            $invoices[] = [
                'id' => $invoice->getId(),
                'number' => $invoice->getNumber(),
                'total' => $invoice->getTotal(),
            ];
        }

        $data = [
            'id' => $customer->getId(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
            'address' => $customer->getAddress(),
            'type' => $customer->getType(),
            'vehicles' => $vehicles,
            'invoices' => $invoices
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/search', methods: ['GET'])]
    public function search(Request $request, CustomerRepository $repo): JsonResponse
    {
        $query = trim($request->query->get('query', ''));

        if ($query === '') {
            return $this->json([]);
        }

        $customers = $repo->search($query);

        $data = array_map(function ($c) {
            return [
                'id' => $c['id'],
                'firstname' => $c['firstname'],
                'lastname' => $c['lastname'],
                'phone' => $c['phone'],
                'email' => $c['email'],

                // valeurs calculées sécurisées avec des valeurs par défaut
                'vehiclesCount' => $c['vehiclesCount'] ?? 0,
                'repairsInProgress' => $c['repairsInProgress'] ?? 0,
                'unpaidInvoices' => $c['unpaidInvoices'] ?? 0,
            ];
        }, $customers);

        return $this->json($data);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse 
    {
        $customer = new Customer();
        $data = json_decode($request->getContent(), true);

        $customer->setType($data['type']);
        $customer->setFirstname($data['firstname']);
        $customer->setLastname($data['lastname']);
        $customer->setPhone($data['phone']);
        $customer->setEmail($data['email']);
        $customer->setAddress($data['address']);

        $em->persist($customer);
        $em->flush();

        $data = [
            'id' => $customer->getId(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
            'address' => $customer->getAddress(),
            'type' => $customer->getType()
        ];

        return $this->json($data, 201);
    }

    #[Route('/list', methods: ['GET'])]
    public function list(CustomerRepository $repo): JsonResponse
    {
        $customers = $repo->findAll();

        $data = array_map(function ($c) {
            return [
                'id' => $c->getId(),
                'name' => $c->getFirstname() . ' ' . $c->getLastname(),
            ];
        }, $customers);

        return new JsonResponse($data);
    }

    #[Route('/{id}', methods:['PUT'])]
    public function edit($id, Request $request, EntityManagerInterface $em, CustomerRepository $repo) : JsonResponse
    {
        $customer = $repo->findOneById($id);
        $data = json_decode($request->getContent(), true);

        $customer->setType($data['type']);
        $customer->setFirstname($data['firstname']);
        $customer->setLastname($data['lastname']);
        $customer->setPhone($data['phone']);
        $customer->setEmail($data['email']);
        $customer->setAddress($data['address']);

        $em->flush();

        $data = [
            'id' => $customer->getId(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
            'address' => $customer->getAddress(),
            'type' => $customer->getType()
        ];

        return $this->json($data, 201);
    }

    #[Route('/{id}' , methods:['DELETE'])]
    public function delete($id, CustomerRepository $repo, EntityManagerInterface $em) : JsonResponse
    {
        $customer = $repo->findOneById($id);

        $em->remove($customer);
        $em->flush();

        return $this->json(['message'=>'Le client à été bien supprimer']);
    }
}
