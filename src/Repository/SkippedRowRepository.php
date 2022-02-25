<?php

namespace App\Repository;

use App\Entity\SkippedRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SkippedRow|null find($id, $lockMode = null, $lockVersion = null)
 * @method SkippedRow|null findOneBy(array $criteria, array $orderBy = null)
 * @method SkippedRow[]    findAll()
 * @method SkippedRow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkippedRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SkippedRow::class);
    }

    // /**
    //  * @return SkippedRow[] Returns an array of SkippedRow objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SkippedRow
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
