<?php

namespace App\Entity;

use App\Repository\RepairLineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepairLineRepository::class)]
class RepairLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'repairLines')]
    private ?Repair $repair = null;

    #[ORM\ManyToOne(inversedBy: 'repairLines')]
    private ?WorkTaskTemplate $WorkTask = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $customTitle = null;

    #[ORM\Column]
    private ?float $laborCost = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\OneToMany(mappedBy: 'repairLine', targetEntity: RepairLineEmployee::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $employees;

    #[ORM\OneToMany(mappedBy: 'repairLine', targetEntity: RepairLinePart::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $parts;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->parts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRepair(): ?Repair
    {
        return $this->repair;
    }

    public function setRepair(?Repair $repair): static
    {
        $this->repair = $repair;

        return $this;
    }

    public function getWorkTask(): ?WorkTaskTemplate
    {
        return $this->WorkTask;
    }

    public function setWorkTask(?WorkTaskTemplate $WorkTask): static
    {
        $this->WorkTask = $WorkTask;

        return $this;
    }

    public function getCustomTitle(): ?string
    {
        return $this->customTitle;
    }

    public function setCustomTitle(?string $customTitle): static
    {
        $this->customTitle = $customTitle;

        return $this;
    }

    public function getLaborCost(): ?float
    {
        return $this->laborCost;
    }

    public function setLaborCost(float $laborCost): static
    {
        $this->laborCost = $laborCost;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function addEmployee(RepairLineEmployee $employee): self
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->setRepairLine($this);
        }
        return $this;
    }

    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addPart(RepairLinePart $part): self
    {
        if (!$this->parts->contains($part)) {
            $this->parts->add($part);
            $part->setRepairLine($this);
        }
        return $this;
    }

    public function getParts(): Collection
    {
        return $this->parts;
    }
}
