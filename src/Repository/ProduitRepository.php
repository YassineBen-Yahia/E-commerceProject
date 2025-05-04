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

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    /**
     * @return Produit[] Returns an array of Produit objects
     */
    public function findByMultipleCategories(array $categories): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.categorie', 'c')
            ->andWhere('c.name IN (:categories)')
            ->setParameter('categories', $categories)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
