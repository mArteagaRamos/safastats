<?php

namespace App\Repository;

use App\Entity\RankingProducto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RankingProducto>
 */
class RankingProductoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RankingProducto::class);
    }

    public function getStatsByCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('rp')
            ->select('rp')
            ->addSelect('AVG(rp.position) AS avgPosition')
            ->join('rp.producto', 'p')
            ->join('rp.ranking', 'r')
            ->join('r.category', 'c')
            ->where('c.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->groupBy('p.id')
            ->orderBy('avgPosition', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return RankingProducto[] Returns an array of RankingProducto objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RankingProducto
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
