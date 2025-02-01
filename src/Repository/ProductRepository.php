<?php

namespace App\Repository;

use App\Entity\OrderItem;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.categories', 'c')
            ->where('c.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
            ->getResult();
    }

    public function findByBestRated($limit): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.reviews', 'r')
            ->leftJoin('p.defaultCategory', 'c')
            ->addSelect('c')
            ->select('p.id, p.name, p.description, p.image, AVG(r.rating) as avgRating, c.id as categoryId')
            ->groupBy('p.id, c.id')
            ->orderBy('avgRating', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByPriceASC($limit): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.price', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

}
