<?php

namespace App\Entity\Order;

use App\Entity\Core\CoreEntity;
use App\Entity\Order\Order;
use App\Entity\Product\Product;
use App\Entity\User\BaseUser;
use App\Entity\Warehouse\Warehouse;
use App\Repository\Order\OrderItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem implements CoreEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
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

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $unitPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalPrice = null;

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
        $this->unitPrice = '0.00';
        $this->totalPrice = '0.00';
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
        $this->calculateTotalPrice();
        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(?string $unitPrice): self
    {
        $this->unitPrice = $unitPrice;
        $this->calculateTotalPrice();
        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(?string $totalPrice): self
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    private function calculateTotalPrice(): void
    {
        if ($this->quantity && $this->unitPrice) {
            $this->totalPrice = bcmul($this->quantity, $this->unitPrice, 2);
        }
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
