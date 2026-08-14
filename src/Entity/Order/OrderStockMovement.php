<?php

namespace App\Entity\Order;

use App\Entity\Core\CoreEntity;
use App\Entity\Product\Product;
use App\Entity\User\BaseUser;
use App\Entity\Warehouse\Warehouse;
use App\Enum\Order\OrderMovementType;
use App\Repository\Order\OrderStockMovementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderStockMovementRepository::class)]
class OrderStockMovement implements CoreEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'stockMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(targetEntity: Warehouse::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Warehouse $warehouse = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $quantity = null;

    #[ORM\Column(enumType: OrderMovementType::class)]
    private ?OrderMovementType $movementType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(targetEntity: BaseUser::class)]
    private ?BaseUser $createdBy = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->quantity = '0.00';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;
        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(?string $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getMovementType(): ?OrderMovementType
    {
        return $this->movementType;
    }

    public function setMovementType(?OrderMovementType $movementType): self
    {
        $this->movementType = $movementType;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedBy(): ?BaseUser
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?BaseUser $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
        return $this;
    }
}
