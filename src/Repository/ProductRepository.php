<?php

namespace App\Repository;

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
/**
     * Trouver les produits les mieux notés.
     */
    public function findByBestRated(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.reviews', 'r')
            ->leftJoin('p.defaultCategory', 'c') // Joindre la catégorie par défaut
            ->addSelect('c') // Inclure la catégorie par défaut dans les résultats
            ->select('p.id, p.name, p.description, p.image, AVG(r.rating) as avgRating, c.id as categoryId') // Ajouter l'ID de la catégorie
            ->groupBy('p.id, c.id') // Grouper par produit et catégorie
            ->orderBy('avgRating', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les produits moins cher.
     */
    public function findByDiscounted(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.defaultCategory', 'c') // Jointure avec la catégorie
            ->select('p.id, p.name, p.description, p.image, p.price, c.id as categoryId') // Inclure l'ID de la catégorie
            ->where('p.price < :threshold')
            ->setParameter('threshold', 100)
            ->orderBy('p.price', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
    
}
