<?php

namespace App\Repository;

use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }
    public function findRecentOrders(): array
    {
        $yesterday = new \DateTimeImmutable('-1 day');

        return $this->createQueryBuilder('o')
            ->where('o.date >= :yesterday')
            ->setParameter('yesterday', $yesterday)
            ->getQuery()
            ->getResult();
    }
}
