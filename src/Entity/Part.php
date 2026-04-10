<?php

namespace App\Entity;

use App\Repository\PartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PartRepository::class)]
class Part
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('part:read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('part:read')]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('part:read')]
    private ?string $reference = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('part:read')]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('part:read')]
    private ?string $image = null;

    #[ORM\Column]
    #[Groups('part:read')]
    private ?float $quantity = null;

    #[ORM\Column(nullable: true)]
    #[Groups('part:read')]
    private ?float $minQuantity = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('part:read')]
    private ?string $provider = null;

    #[ORM\Column(nullable: true)]
    #[Groups('part:read')]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'parts')]
    #[Groups('part:read')]
    private ?Category $category = null;

    /**
     * @var Collection<int, StockMovement>
     */
    #[ORM\OneToMany(targetEntity: StockMovement::class, mappedBy: 'part')]
    private Collection $stockMovements;

    /**
     * @var Collection<int, RepairLinePart>
     */
    #[ORM\OneToMany(targetEntity: RepairLinePart::class, mappedBy: 'part')]
    private Collection $repairLineParts;

    public function __construct()
    {
        $this->stockMovements = new ArrayCollection();
        $this->repairLineParts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getMinQuantity(): ?float
    {
        return $this->minQuantity;
    }

    public function setMinQuantity(?float $minQuantity): static
    {
        $this->minQuantity = $minQuantity;

        return $this;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(?string $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, StockMovement>
     */
    public function getStockMovements(): Collection
    {
        return $this->stockMovements;
    }

    public function addStockMovement(StockMovement $stockMovement): static
    {
        if (!$this->stockMovements->contains($stockMovement)) {
            $this->stockMovements->add($stockMovement);
            $stockMovement->setPart($this);
        }

        return $this;
    }

    public function removeStockMovement(StockMovement $stockMovement): static
    {
        if ($this->stockMovements->removeElement($stockMovement)) {
            // set the owning side to null (unless already changed)
            if ($stockMovement->getPart() === $this) {
                $stockMovement->setPart(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RepairLinePart>
     */
    public function getRepairLineParts(): Collection
    {
        return $this->repairLineParts;
    }

    public function addRepairLinePart(RepairLinePart $repairLinePart): static
    {
        if (!$this->repairLineParts->contains($repairLinePart)) {
            $this->repairLineParts->add($repairLinePart);
            $repairLinePart->setPart($this);
        }

        return $this;
    }

    public function removeRepairLinePart(RepairLinePart $repairLinePart): static
    {
        if ($this->repairLineParts->removeElement($repairLinePart)) {
            // set the owning side to null (unless already changed)
            if ($repairLinePart->getPart() === $this) {
                $repairLinePart->setPart(null);
            }
        }

        return $this;
    }
}
