<?php

namespace App\Entity;

use App\Repository\RepairRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepairRepository::class)]
class Repair
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'repairs')]
    private ?Vehicle $vehicle = null;

    private ?int $total = null;

    /**
     * @var Collection<int, RepairLine>
     */
    #[ORM\OneToMany(targetEntity: RepairLine::class, mappedBy: 'repair')]
    private Collection $repairLines;

    public function __construct()
    {
        $this->repairLines = new ArrayCollection();
    }

    public function calculateTotal(): self
    {
        $total = 0;

        foreach ($this->repairLines as $line) {
            $total += $line->getTotal();
        }

        $this->total = $total;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

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
            $repairLine->setRepair($this);
        }

        return $this;
    }

    public function removeRepairLine(RepairLine $repairLine): static
    {
        if ($this->repairLines->removeElement($repairLine)) {
            // set the owning side to null (unless already changed)
            if ($repairLine->getRepair() === $this) {
                $repairLine->setRepair(null);
            }
        }

        return $this;
    }

    public function getTotal() : ?float
    {
        return $this->total;
    }

    #[ORM\OneToOne(mappedBy: 'repair', targetEntity: Invoice::class)]
    private ?Invoice $invoice = null;

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;
        return $this;
    }

}
