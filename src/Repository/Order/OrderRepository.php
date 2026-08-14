<?php

namespace App\Repository\Order;

use App\Entity\Order\Order;
use App\Repository\Core\CoreRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository implements CoreRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getPaginatedQuery(array $searchFormData = []): Query
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.client', 'c')
            ->addSelect('c');

        // Търсене по клиент
        if (!empty($searchFormData['client'])) {
            $qb->andWhere('c.name LIKE :client')
                ->setParameter('client', '%' . $searchFormData['client'] . '%');
        }

        // Търсене по дата на поръчка
        if (!empty($searchFormData['orderDateFrom'])) {
            $qb->andWhere('o.orderDate >= :orderDateFrom')
                ->setParameter('orderDateFrom', $searchFormData['orderDateFrom']);
        }

        if (!empty($searchFormData['orderDateTo'])) {
            $qb->andWhere('o.orderDate <= :orderDateTo')
                ->setParameter('orderDateTo', $searchFormData['orderDateTo']);
        }


        // Сортиране по дата на създаване (най-нови първи)
        $qb->orderBy('o.createdAt', 'DESC');

        return $qb->getQuery();
    }
}
