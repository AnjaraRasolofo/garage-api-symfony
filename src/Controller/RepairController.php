<?php

namespace App\Controller;

use App\Entity\Repair;
use App\Entity\RepairLine;
use App\Entity\RepairLineEmployee;
use App\Entity\RepairLinePart;
use App\Repository\VehicleRepository;
use App\Repository\EmployeeRepository;
use App\Repository\PartRepository;
use App\Repository\RepairRepository;
use App\Repository\WorkTaskTemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/repairs')]
final class RepairController extends AbstractController
{

    #[Route('', methods: ['GET'])]
    public function index(Request $request, RepairRepository $repo): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, (int) $request->query->get('limit', 10));
        $search = $request->query->get('search', '');

        $result = $repo->findPaginatedWithSearch($page, $limit, $search);

        $reparations = $result['data'];
        $total = $result['total'];

        $data = array_map(function ($r) {
            return [
                'id' => $r->getId(),
                'date' => $r->getDate()?->format('Y-m-d'),
                'status' => $r->getStatus(),

                // Relation véhicule
                'vehicle' => $r->getVehicle() ? [
                    'id' => $r->getVehicle()->getId(),
                    'number' => $r->getVehicle()->getNumber(), // ou immatriculation
                ] : null,

                // Relation client (via véhicule )
                'customer' => $r->getVehicle() && $r->getVehicle()->getCustomer() ? [
                    'id' => $r->getVehicle()->getCustomer()->getId(),
                    'name' => $r->getVehicle()->getCustomer()->getFirstname() . "" . $r->getVehicle()->getCustomer()->getLastname(),
                ] : null,
            ];
        }, $reparations);

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

    #[Route('/stats', methods: ['GET'])]
    public function repairStats(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $filter = $request->query->get('filter', 'day');

        $qb = $em->createQueryBuilder()
            ->select('COUNT(DISTINCT r.vehicle) as total')
            ->from(Repair::class, 'r');

        $now = new \DateTime();

        if ($filter === 'day') {
            $qb->where('DATE(r.date) = :today')
            ->setParameter('today', $now->format('Y-m-d'));
        }

        if ($filter === 'week') {
            $start = (clone $now)->modify('monday this week')->setTime(0,0,0);
            $end = (clone $start)->modify('+6 days')->setTime(23,59,59);

            $qb->where('r.date BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end);
        }

        if ($filter === 'month') {
            $qb->where('MONTH(r.date) = :month')
            ->andWhere('YEAR(r.date) = :year')
            ->setParameter('month', $now->format('m'))
            ->setParameter('year', $now->format('Y'));
        }

        $result = $qb->getQuery()->getSingleScalarResult();

        return $this->json([
            'total' => (int)$result
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, RepairRepository $repo): JsonResponse
    {
        $repair = $repo->find($id);

        if (!$repair) {
            return $this->json(['message' => 'Réparation introuvable'], 404);
        }

        $vehicle = $repair->getVehicle();
        $customer = $vehicle?->getCustomer();

        $data = [
            'id' => $repair->getId(),
            'vehicle_id' => $vehicle?->getId(),

            'vehicle' => $vehicle ? [
                'id' => $vehicle->getId(),
                'number' => $vehicle->getNumber(),
                'model' => $vehicle->getModel()
            ] : null,

            'client' => $customer 
                ? $customer->getFirstname() . ' ' . $customer->getLastname()
                : '',

            'date' => $repair->getDate()?->format('Y-m-d'),
            'status' => $repair->getStatus(),

            // LINES (travaux)
            'lines' => array_map(function ($line) {
                $employee = $line->getEmployees()->first();

                return [
                    'template_id' => $line->getWorkTask()?->getId(),
                    'custom_title' => $line->getCustomTitle(),
                    'labor_cost' => $line->getLaborCost(),
                    'technician' => $employee?->getRole(),
                    'hours' => $employee?->getHours(),
                    'employee_id' => $employee?->getEmployee()?->getId()
                ];
            }, $repair->getRepairLines()->toArray()),

            // PARTS 
            'parts' => array_merge(...array_map(function ($line) {
                return array_map(function ($p) {
                    return [
                        'part_id' => $p->getPart()?->getId(),
                        'name' => $p->getPart()?->getName(),
                        'quantity' => $p->getQuantity(),
                        'price' => $p->getPrice()
                    ];
                }, $line->getParts()->toArray());
            }, $repair->getRepairLines()->toArray()))
        ];

        return $this->json($data);
    }

    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        VehicleRepository $vehicleRepo,
        EmployeeRepository $employeeRepo,
        PartRepository $partRepo,
        WorkTaskTemplateRepository $templateRepo
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $this->json([
                'message' => 'Invalid JSON',
                'error' => json_last_error_msg()
            ], 400);
        }    

        if (!isset($data['vehicle_id'], $data['lines'])) {
            return $this->json(['message' => 'Invalid data'], 400);
        }

        $vehicle = $vehicleRepo->find($data['vehicle_id']);
        if (!$vehicle) {
            return $this->json(['message' => 'Vehicle not found'], 404);
        }

        $repair = new Repair();
        $repair->setVehicle($vehicle);
        $repair->setStatus($data['status'] ?? 'pending');
        $repair->setDate(
            isset($data['date'])
                ? new \DateTime($data['date'])
                : new \DateTime()
        );

        try {
            $em->beginTransaction();

            // Lignes
            foreach ($data['lines'] as $lineData) {

                $line = new RepairLine();

                $template = null;

                if (!empty($lineData['template_id'])) {
                    $template = $templateRepo->find($lineData['template_id']);
                }

                // toujours stocker le désignation de travaux personnalisés
                $customTitle = trim($lineData['work_task'] ?? '');

                if ($customTitle === '') {
                    throw new \Exception("Le travaux est obligatoire est obligatoire");
                }

                $line->setCustomTitle($customTitle);

                // template optionnel
                $line->setWorkTask($template);

                $line->setLaborCost($lineData['labor_cost'] ?? 0);

                // EMPLOYEES
                foreach ($lineData['employees'] ?? [] as $empData) {

                    $employee = $employeeRepo->find($empData['employee_id']);
                    if (!$employee) continue; 

                    $rle = new RepairLineEmployee();
                    $rle->setEmployee($employee);
                    $rle->setRole($empData['role'] ?? null);
                    $rle->setHours($empData['hours'] ?? null);
                    $rle->setCost($empData['cost'] ?? 0);

                    $line->addEmployee($rle); //
                    $em->persist($rle);
                }

                // PARTS
                foreach ($lineData['parts'] ?? [] as $partData) {

                    $part = $partRepo->find($partData['part_id']);
                    if (!$part) continue;


                    $quantity = $partData['quantity'] ?? 0;
                    $price = $partData['price'] ?? 0;
                    $title = $partData['title'] ?? null;

                    $rlp = new RepairLinePart();
                    $rlp->setPart($part);
                    $rlp->setQuantity($quantity);
                    $rlp->setPrice($price);
                    $rlp->setTotal($quantity * $price);

                    // si tu utilises title quelque part
                    // $rlp->setTitle($title); (si champ existe)

                    $line->addPart($rlp);
                }

                $lineTotal = $line->getLaborCost();

                foreach ($line->getParts() as $p) {
                    $lineTotal += $p->getTotal();
                }

                $line->setTotal($lineTotal);

                  $repair->addRepairLine($line);
                  $em->persist($line);
            }

            // Calcul total réparation
            $repair->calculateTotal();

            $em->persist($repair);
            $em->flush();
            $em->commit();

            return $this->json([
                'id' => $repair->getId(),
                'total' => $repair->getTotal(),
                'message' => 'Repair created successfully'
            ], 201);

        } catch (\Exception $e) {
            $em->rollback();

            return $this->json([
                'message' => 'Error creating repair',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}