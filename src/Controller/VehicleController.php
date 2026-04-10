<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\CustomerRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/vehicles')]
final class VehicleController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, VehicleRepository $repo): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, (int) $request->query->get('limit', 20));
        $search = $request->query->get('search', '');

        $result = $repo->findPaginatedWithSearch($page, $limit, $search);

        $vehicles = $result['vehicles'];
        $total = $result['total'];

        // formatage JSON
        $data = array_map(fn($v) => [
            'id' => $v->getId(),
            'brand' => $v->getBrand(),
            'model' => $v->getModel(),
            'number' => $v->getNumber(),
            'year' => $v->getYear(),
            'color' => $v->getColor(),
            'fuelType' => $v->getFuelType(),
            'mileage' => $v->getMileage(),
            'vin' => $v->getVin(),
            'engineNumber' => $v->getEngineNumber(),
            'status' => $v->getStatus(),
            'insuranceExpiryDate' => $v->getInsuranceExpiryDate()?->format('Y-m-d'),
            'lastServiceDate' => $v->getLastServiceDate()?->format('Y-m-d'),
            'customer' => $v->getCustomer() ? [
                'id' => $v->getCustomer()->getId(),
                'firstname' => $v->getCustomer()->getFirstname(),
                'lastname' => $v->getCustomer()->getLastname(),
            ] : null,
        ], $vehicles);

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

    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show($id, VehicleRepository $repo): JsonResponse
    {
        $vehicle = $repo->find($id);

        if (!$vehicle) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $vehicle->getId(),
            'brand' => $vehicle->getBrand(),
            'model' => $vehicle->getModel(),
            'number' => $vehicle->getNumber(),
            'year' => $vehicle->getYear(),
            'color' => $vehicle->getColor(),

            // On inclut le customer MAIS sans ses vehicles
            'customer' => $vehicle->getCustomer() ? [
                'id' => $vehicle->getCustomer()->getId(),
                'firstname' => $vehicle->getCustomer()->getFirstname(),
                'lastname' => $vehicle->getCustomer()->getLastname(),
                'name' => $vehicle->getCustomer()->getFirstname() . ' ' . $vehicle->getCustomer()->getLastname(),
            ] : null,
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/customer/{id}', methods: ['GET'])]
    public function customerVehicles($id, Request $request, VehicleRepository $repo): JsonResponse
    {
        $customerId = $request->query->get('id');

        if ($customerId) {
            $vehicles = $repo->findBy(['customer' => $customerId]);
        } else {
            $vehicles = $repo->findAll();
        }

        $data = array_map(function ($v) {
            return [
                'id' => $v->getId(),
                'brand' => $v->getBrand(),
                'model' => $v->getModel(),
                'number' => $v->getNumber()
            ];
        }, $vehicles);

        return $this->json($data);
    }

    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        CustomerRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        //dd($request->getContent());
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        // Validation minimale
        $requiredFields = ['brand', 'model', 'number', 'year', 'customerId'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return $this->json(['error' => "$field is required"], 400);
            }
        }

        // Récupération du client
        $customer = $repo->find($data['customerId']);

        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], 404);
        }

        // Création du véhicule
        $vehicle = new Vehicle();

        $vehicle->setBrand($data['brand']);
        $vehicle->setModel($data['model']);
        $vehicle->setNumber($data['number']);
        $vehicle->setYear((int) $data['year']);
        $vehicle->setColor($data['color'] ?? null);

        // champs avancés
        $vehicle->setFuelType($data['fuelType'] ?? null);
        $vehicle->setMileage(isset($data['mileage']) ? (int) $data['mileage'] : null);
        $vehicle->setVin($data['vin'] ?? null);

        // relation
        $vehicle->setCustomer($customer);

        // (optionnel si déjà dans constructeur)
        // $vehicle->setCreatedAt(new \DateTime());

        // Sauvegarde
        $em->persist($vehicle);
        $em->flush();

        // Réponse propre (évite boucle infinie JSON)
        return $this->json([
            'id' => $vehicle->getId(),
            'brand' => $vehicle->getBrand(),
            'model' => $vehicle->getModel(),
            'number' => $vehicle->getNumber(),
            'year' => $vehicle->getYear(),
            'color' => $vehicle->getColor(),
            'fuelType' => $vehicle->getFuelType(),
            'mileage' => $vehicle->getMileage(),
            'vin' => $vehicle->getVin(),
            'customer' => [
                'id' => $customer->getId(),
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
            ]
        ], 201);
    }

    #[Route('/{id}', methods:['PUT'])]
    public function edit($id, Request $request, EntityManagerInterface $em, CustomerRepository $repoC, VehicleRepository $repoV) : JsonResponse
    {
        
        $data = json_decode($request->getContent(), true);
        $vehicle = $repoV->findOneById($id);
        $customer = $repoC->findOneById($data['customerId']);

        $vehicle->setBrand($data['brand']);
        $vehicle->setModel($data['model']);
        $vehicle->setNumber($data['number']);
        $vehicle->setYear($data['year']);
        $vehicle->setColor($data['color'] ?? null);
        $vehicle->setFuelType($data['fuelType'] ?? null);
        $vehicle->setMileage($data['mileage'] ?? null);
        $vehicle->setVin($data['vin'] ?? null);

        // relation avec Customer
        $vehicle->setCustomer($customer);

        $em->flush();

        // Réponse propre (évite boucle infinie JSON)
        return $this->json([
            'id' => $vehicle->getId(),
            'brand' => $vehicle->getBrand(),
            'model' => $vehicle->getModel(),
            'number' => $vehicle->getNumber(),
            'year' => $vehicle->getYear(),
            'color' => $vehicle->getColor(),
            'fuelType' => $vehicle->getFuelType(),
            'mileage' => $vehicle->getMileage(),
            'vin' => $vehicle->getVin(),
            'customer' => [
                'id' => $customer->getId(),
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
            ]
        ], 201);
    }

    #[Route('/{id}' , methods:['DELETE'])]
    public function delete($id, VehicleRepository $repo, EntityManagerInterface $em) : JsonResponse
    {
        $vehicle = $repo->findOneById($id);

        $em->remove($vehicle);
        $em->flush();

        return $this->json(['message'=>'Le véhicule à été bien supprimer']);
    }
}
