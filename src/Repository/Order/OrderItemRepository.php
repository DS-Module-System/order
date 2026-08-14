<?php

namespace App\Repository\Order;

use App\Entity\Order\OrderItem;
use App\Repository\Core\CoreRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class OrderItemRepository extends ServiceEntityRepository implements CoreRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    public function getPaginatedQuery(array $searchFormData = []): Query
    {
        $qb = $this->createQueryBuilder('oi')
            ->leftJoin('oi.order', 'o')
            ->leftJoin('oi.product', 'p')
            ->leftJoin('oi.warehouse', 'w')
            ->addSelect('o', 'p', 'w');

        // Филтриране по поръчка
        if (!empty($searchFormData['orderId'])) {
            $qb->andWhere('o.id = :orderId')
                ->setParameter('orderId', $searchFormData['orderId']);
        }

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

        // Сортиране по ID (най-нови първи)
        $qb->orderBy('oi.id', 'DESC');

        return $qb->getQuery();
    }
}
