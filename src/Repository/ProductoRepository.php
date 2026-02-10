<?php

namespace App\Repository;

use App\Entity\Producto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Producto>
 */
class ProductoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }

    public function findByTypeWithAvgRating(
        ?string $type = null,
        int $limit = 20,
        int $offset = 0
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.reviews', 'r')
            ->addSelect('p')
            ->addSelect('COALESCE(AVG(r.stars), 0) AS avgRating')
            ->groupBy('p.id')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($type) {
            $qb->andWhere('p.productType = :type')
                ->setParameter('type', $type);
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllWithAvgRating(int $limit = 20, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.reviews', 'r')
            ->addSelect('COALESCE(AVG(r.stars), 0) AS avgStars')
            ->groupBy('p.id')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    public function findAllProductTypes(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('DISTINCT p.productType')
            ->where('p.productType IS NOT NULL');

        $result = $qb->getQuery()->getResult();

        return array_map(fn($row) => $row['productType'], $result);
    }

    //    /**
    //     * @return Producto[] Returns an array of Producto objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Producto
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
