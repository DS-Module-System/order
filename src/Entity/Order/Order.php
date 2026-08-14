<?php

namespace App\Entity\Order;

use App\Entity\Core\CoreEntity;
use App\Entity\Client\Client;
use App\Entity\User\BaseUser;
use App\Entity\Warehouse\Warehouse;
use App\Repository\Order\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order implements CoreEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $orderDate = null;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(targetEntity: BaseUser::class)]
    private ?BaseUser $createdBy = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalPrice = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'])]
    private Collection $items;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderStockMovement::class, cascade: ['persist', 'remove'])]
    private Collection $stockMovements;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->orderDate = new \DateTimeImmutable();
        $this->totalPrice = '0.00';
        $this->items = new ArrayCollection();
        $this->stockMovements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(?\DateTimeInterface $orderDate): self
    {
        $this->orderDate = $orderDate;
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

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(?string $totalPrice): self
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function calculateTotalPrice(): self
    {
        $total = '0.00';

        foreach ($this->items as $item) {
            if ($item->getTotalPrice()) {
                $total = bcadd($total, $item->getTotalPrice(), 2);
            }
        }

        $this->totalPrice = $total;
        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderStockMovement>
     */
    public function getStockMovements(): Collection
    {
        return $this->stockMovements;
    }

    public function addStockMovement(OrderStockMovement $stockMovement): self
    {
        if (!$this->stockMovements->contains($stockMovement)) {
            $this->stockMovements->add($stockMovement);
            $stockMovement->setOrder($this);
        }

        return $this;
    }

    public function removeStockMovement(OrderStockMovement $stockMovement): self
    {
        if ($this->stockMovements->removeElement($stockMovement)) {
            // set the owning side to null (unless already changed)
            if ($stockMovement->getOrder() === $this) {
                $stockMovement->setOrder(null);
            }
        }

        return $this;
    }
}
