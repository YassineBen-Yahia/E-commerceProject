<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

        /**
         * @return Produit[] Returns an array of Produit objects
         */
        public function findByCategory($value): array
        {
            return $this->createQueryBuilder('p')
                ->join('p.categorie', 'c')
                ->andWhere('c.name = :val')
                ->setParameter('val', $value)
                ->orderBy('p.id', 'ASC')
//                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }


//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByMultipleCategories(array $categories): array
//    {
//        return $this->createQueryBuilder('p')
//            ->join('p.categorie', 'c')
//            ->andWhere('c.name IN (:categories)')
//            ->setParameter('categories', $categories)
//            ->orderBy('p.id', 'ASC')
//            ->getQuery()
//            ->getResult();
//    }

    public function countByCategory(string $category): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->join('p.categorie', 'c')
            ->where('c.name = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countByCategories(string $categoriesString): int
    {
        $categories = array_map('trim', explode(',', $categoriesString));

        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->join('p.categorie', 'c')
            ->where('c.name IN (:categories)')
            ->setParameter('categories', $categories)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findByCategoryAndLimit(string $category, int $limit, int $offset): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.categorie', 'c')
            ->where('c.name = :category')
            ->setParameter('category', $category)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function findByFilterAndLimit(string $categoriesString, int $limit, int $offset, float $min, float $max ): array
    {
        $categories = array_map('trim', explode(',', $categoriesString));

        return $this->createQueryBuilder('p')
            ->join('p.categorie', 'c')
            ->where('c.name IN (:categories)')
            ->andWhere('p.price BETWEEN :min AND :max')
            ->setParameter('categories', $categories)
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
    public function findByPriceRange( float $min, float $max ): array
    {

        return $this->createQueryBuilder('p')
            ->Where('p.price BETWEEN :min AND :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->getQuery()
            ->getResult();
    }
}
