<?php

namespace App\Entity;

use App\Repository\RepairLineEmployeeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepairLineEmployeeRepository::class)]
class RepairLineEmployee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?RepairLine $repairLine = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $role = null;

    #[ORM\Column(nullable: true)]
    private ?float $hours = null;

    #[ORM\Column]
    private ?float $cost = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRepairLine(): ?RepairLine
    {
        return $this->repairLine;
    }

    public function setRepairLine(?RepairLine $repairLine): static
    {
        $this->repairLine = $repairLine;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getHours(): ?float
    {
        return $this->hours;
    }

    public function setHours(?float $hours): static
    {
        $this->hours = $hours;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): static
    {
        $this->cost = $cost;

        return $this;
    }
}
