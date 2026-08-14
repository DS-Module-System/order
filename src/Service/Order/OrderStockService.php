<?php

namespace App\Service\Order;

use App\Entity\Order\Order;
use App\Entity\Order\OrderItem;
use App\Entity\Order\OrderStockMovement;
use App\Entity\Warehouse\WarehouseStock;
use App\Enum\Order\OrderMovementType;
use App\Repository\Warehouse\WarehouseStockRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderStockService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WarehouseStockRepository $warehouseStockRepository
    ) {}

    /**
     * Изписва продукти от склада за поръчка
     */
    public function removeProductsFromWarehouse(OrderItem $item): void
    {
        if (!$item->getWarehouse() || !$item->getProduct()) {
            return;
        }

        $warehouseStock = $this->warehouseStockRepository->findOneBy([
            'warehouse' => $item->getWarehouse(),
            'product' => $item->getProduct()
        ]);

        if (!$warehouseStock) {
            throw new \Exception('Няма наличности от този продукт в избрания склад');
        }

        $currentQuantity = (float) $warehouseStock->getQuantity();
        $requiredQuantity = (float) $item->getQuantity();

        if ($currentQuantity < $requiredQuantity) {
            throw new \Exception('Недостатъчни наличности от продукт в склада');
        }

        // Изписваме използваното количество
        $newQuantity = $currentQuantity - $requiredQuantity;
        $warehouseStock->setQuantity((string) $newQuantity);
        $warehouseStock->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($warehouseStock);

        // Създаваме запис за складово движение
        $this->createStockMovement($item, OrderMovementType::OUT);

        $this->entityManager->flush();
    }

    /**
     * Проверява дали има достатъчно наличности от продукт в склада
     */
    public function checkProductAvailability(OrderItem $item): bool
    {
        if (!$item->getWarehouse() || !$item->getProduct()) {
            return false;
        }

        $warehouseStock = $this->warehouseStockRepository->findOneBy([
            'warehouse' => $item->getWarehouse(),
            'product' => $item->getProduct()
        ]);

        if (!$warehouseStock) {
            return false;
        }

        $currentQuantity = (float) $warehouseStock->getQuantity();
        $requiredQuantity = (float) $item->getQuantity();

        return $currentQuantity >= $requiredQuantity;
    }

    /**
     * Връща наличностите от продукт в склада
     */
    public function getProductStock(OrderItem $item): float
    {
        if (!$item->getWarehouse() || !$item->getProduct()) {
            return 0.0;
        }

        $warehouseStock = $this->warehouseStockRepository->findOneBy([
            'warehouse' => $item->getWarehouse(),
            'product' => $item->getProduct()
        ]);

        if (!$warehouseStock) {
            return 0.0;
        }

        return (float) $warehouseStock->getQuantity();
    }

    /**
     * Създава запис за складово движение
     */
    private function createStockMovement(OrderItem $item, OrderMovementType $movementType): void
    {
        $stockMovement = new OrderStockMovement();
        $stockMovement->setOrder($item->getOrder());
        $stockMovement->setProduct($item->getProduct());
        $stockMovement->setWarehouse($item->getWarehouse());
        $stockMovement->setQuantity($item->getQuantity());
        $stockMovement->setMovementType($movementType);
        $stockMovement->setNotes('Автоматично генерирано при изписване на продукт за поръчка');
        $stockMovement->setCreatedBy($item->getCreatedBy());
        $stockMovement->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($stockMovement);
    }
}
