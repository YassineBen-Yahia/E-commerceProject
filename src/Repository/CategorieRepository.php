<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }



        /**
         * @return Categorie[] Returns an array of Categorie objects
         */
        public function findByName($value): array
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.name = :val')
                ->setParameter('val', $value)
                ->orderBy('c.id', 'ASC')
//                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }

        public function findStockInCategory()
        {
            return $this->createQueryBuilder('c')
                ->select('SUM(p.stock) as totalStock')
                ->addSelect('c.name as categoryName')
                ->join('c.Produits', 'p')
                ->groupBy('c.id')
                ->getQuery()
                ->getResult()
            ;

        }
        public function findOneById($id): ?Categorie
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.id = :val')
                ->setParameter('val', $id)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
}
