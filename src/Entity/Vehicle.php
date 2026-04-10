<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['vehicle:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['vehicle:read'])]
    private ?string $brand = null;

    #[ORM\Column(length: 100)]
    #[Groups(['vehicle:read'])]
    private ?string $model = null;

    #[ORM\Column(length: 50)]
    #[Groups(['vehicle:read'])]
    private ?string $number = null;

    #[ORM\Column]
    #[Groups(['vehicle:read'])]
    private ?int $year = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $fuelType = null;

    #[ORM\Column(nullable: true)]
    private ?int $mileage = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $vin = null;

    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vehicle:read'])]
    private ?Customer $customer = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $engineNumber = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $insuranceExpiryDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $lastServiceDate = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = null;

    /**
     * @var Collection<int, Repair>
     */
    #[ORM\OneToMany(targetEntity: Repair::class, mappedBy: 'vehicle')]
    private Collection $repairs;

    public function __construct()
    {
        $this->repairs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getFuelType(): ?string
    {
        return $this->fuelType;
    }

    public function setFuelType(?string $fuelType): static
    {
        $this->fuelType = $fuelType;

        return $this;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(?int $mileage): static
    {
        $this->mileage = $mileage;

        return $this;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(?string $vin): static
    {
        $this->vin = $vin;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getEngineNumber(): ?string
    {
        return $this->engineNumber;
    }

    public function setEngineNumber(?string $engineNumber): static
    {
        $this->engineNumber = $engineNumber;

        return $this;
    }

    public function getInsuranceExpiryDate(): ?\DateTime
    {
        return $this->insuranceExpiryDate;
    }

    public function setInsuranceExpiryDate(?\DateTime $insuranceExpiryDate): static
    {
        $this->insuranceExpiryDate = $insuranceExpiryDate;

        return $this;
    }

    public function getLastServiceDate(): ?\DateTime
    {
        return $this->lastServiceDate;
    }

    public function setLastServiceDate(?\DateTime $lastServiceDate): static
    {
        $this->lastServiceDate = $lastServiceDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Repair>
     */
    public function getRepairs(): Collection
    {
        return $this->repairs;
    }

    public function addRepair(Repair $repair): static
    {
        if (!$this->repairs->contains($repair)) {
            $this->repairs->add($repair);
            $repair->setVehicle($this);
        }

        return $this;
    }

    public function removeRepair(Repair $repair): static
    {
        if ($this->repairs->removeElement($repair)) {
            // set the owning side to null (unless already changed)
            if ($repair->getVehicle() === $this) {
                $repair->setVehicle(null);
            }
        }

        return $this;
    }
}
