<?php

namespace App\Service\Order;

use App\Entity\Order\Order;
use App\Entity\Order\OrderItem;
use App\Entity\Warehouse\WarehouseStock;
use App\Repository\Warehouse\WarehouseStockRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    private EntityManagerInterface $entityManager;
    private WarehouseStockRepository $warehouseStockRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        WarehouseStockRepository $warehouseStockRepository
    ) {
        $this->entityManager = $entityManager;
        $this->warehouseStockRepository = $warehouseStockRepository;
    }

    /**
     * Добавя продукт в поръчка от определен склад
     */
    public function addProductToOrder(Order $order, WarehouseStock $warehouseStock, float $quantity, float $unitPrice): OrderItem
    {
        // Проверяваме дали има достатъчно количество в склада
        if ($warehouseStock->getQuantity() < $quantity) {
            throw new \InvalidArgumentException('Недостатъчно количество в склада');
        }

        // Създаваме нов OrderItem
        $orderItem = new OrderItem();
        $orderItem->setOrder($order);
        $orderItem->setProduct($warehouseStock->getProduct());
        $orderItem->setWarehouseStock($warehouseStock);
        $orderItem->setQuantity((string) $quantity);
        $orderItem->setAvailableQuantity((string) $warehouseStock->getQuantity());
        $orderItem->setUnitPrice((string) $unitPrice);

        // Добавяме в поръчката
        $order->addItem($orderItem);

        // Записваме в базата данни
        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();

        return $orderItem;
    }

    /**
     * Връща наличните продукти в склада за поръчка
     */
    public function getAvailableProductsForOrder(Order $order): array
    {
        $warehouse = $order->getWarehouse();
        if (!$warehouse) {
            return [];
        }

        return $this->warehouseStockRepository->findBy(['warehouse' => $warehouse]);
    }

    /**
     * Изчислява общата сума на поръчката
     */
    public function calculateOrderTotal(Order $order): float
    {
        $total = 0.0;
        
        foreach ($order->getItems() as $item) {
            $total += (float) $item->getTotalPrice();
        }

        return $total;
    }
}
