<?php

namespace App\Entity;

use App\Repository\WorkTaskTemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkTaskTemplateRepository::class)]
class WorkTaskTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $defaultLaborCost = null;

    /**
     * @var Collection<int, RepairLine>
     */
    #[ORM\OneToMany(targetEntity: RepairLine::class, mappedBy: 'WorkTask')]
    private Collection $repairLines;

    public function __construct()
    {
        $this->repairLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDefaultLaborCost(): ?float
    {
        return $this->defaultLaborCost;
    }

    public function setDefaultLaborCost(float $defaultLaborCost): static
    {
        $this->defaultLaborCost = $defaultLaborCost;

        return $this;
    }

    /**
     * @return Collection<int, RepairLine>
     */
    public function getRepairLines(): Collection
    {
        return $this->repairLines;
    }

    public function addRepairLine(RepairLine $repairLine): static
    {
        if (!$this->repairLines->contains($repairLine)) {
            $this->repairLines->add($repairLine);
            $repairLine->setWorkTask($this);
        }

        return $this;
    }

    public function removeRepairLine(RepairLine $repairLine): static
    {
        if ($this->repairLines->removeElement($repairLine)) {
            // set the owning side to null (unless already changed)
            if ($repairLine->getWorkTask() === $this) {
                $repairLine->setWorkTask(null);
            }
        }

        return $this;
    }
}
