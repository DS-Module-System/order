<?php

namespace App\Repository\Order;

use App\Entity\Order\OrderStockMovement;
use App\Repository\Core\CoreRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class OrderStockMovementRepository extends ServiceEntityRepository implements CoreRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderStockMovement::class);
    }

    public function getPaginatedQuery(array $searchFormData = []): Query
    {
        $qb = $this->createQueryBuilder('osm')
            ->leftJoin('osm.order', 'o')
            ->leftJoin('osm.product', 'p')
            ->leftJoin('osm.warehouse', 'w')
            ->addSelect('o', 'p', 'w');

        // Търсене по продукт
        if (!empty($searchFormData['product'])) {
            $qb->andWhere('p.name LIKE :product')
                ->setParameter('product', '%' . $searchFormData['product'] . '%');
        }

        // Търсене по склад
        if (!empty($searchFormData['warehouse'])) {
            $qb->andWhere('w.name LIKE :warehouse')
                ->setParameter('warehouse', '%' . $searchFormData['warehouse'] . '%');
        }

        // Търсене по тип движение
        if (!empty($searchFormData['movementType'])) {
            $qb->andWhere('osm.movementType = :movementType')
                ->setParameter('movementType', $searchFormData['movementType']);
        }

        // Сортиране по ID (най-нови първи)
        $qb->orderBy('osm.id', 'DESC');

        return $qb->getQuery();
    }
}
