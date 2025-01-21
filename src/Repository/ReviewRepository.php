<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Enum\ReviewStatusEnum;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function findValidReviewsForProduct($product): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.status = :status')
            ->setParameter('status', ReviewStatusEnum::VALIDATED)
            ->andWhere('r.product = :product')
            ->setParameter('product', $product)
            ->orderBy('r.datePublication', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
