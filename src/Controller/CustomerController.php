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
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, (int) $request->query->get('limit', 20));
        $search = $request->query->get('search', '');

        $qb = $repo->createQueryBuilder('c');

        // filtre recherche
        if (!empty($search)) {
            $qb->andWhere('
                c.firstname LIKE :search OR
                c.lastname LIKE :search OR
                c.type LIKE :search
            ')
            ->setParameter('search', '%' . $search . '%');
        }

        // total filtré
        $total = (clone $qb)
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // pagination
        $customers = $qb
            ->orderBy('c.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        // format
        $data = array_map(fn($c) => [
            'id' => $c->getId(),
            'firstname' => $c->getFirstname(),
            'lastname' => $c->getLastname(),
            'email' => $c->getEmail(),
            'phone' => $c->getPhone(),
            'type' => $c->getType()
        ], $customers);

        return $this->json([
            'data' => $data,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => (int) $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show($id, CustomerRepository $repo): JsonResponse
    {
        $customer = $repo->find($id);

        if (!$customer) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $customer->getId(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
            'address' => $customer->getAddress(),
            'type' => $customer->getType()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
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
