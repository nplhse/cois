<?php

namespace App\Repository;

use App\Entity\Allocation;
use App\Entity\Hospital;
use App\Entity\Import;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Allocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Allocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Allocation[]    findAll()
 * @method Allocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AllocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Allocation::class);
    }

    public function countAllocations(Hospital $hospital = null): string
    {
        if ($hospital) {
            $qb = $this->createQueryBuilder('a')
                ->select('COUNT(a.id)')
                ->andWhere('a.hospital = :hospital')
                ->setParameter('hospital', $hospital->getId())
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            $qb = $this->createQueryBuilder('a')
                ->select('COUNT(a.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }

        return $qb;
    }

    public function countAllocationsByAge(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(DISTINCT a.id)')
            ->groupBy('a.age')
            ->orderBy('a.age', 'DESC')
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function countAllocationsByGender(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(DISTINCT a.gender) AS counter')
            ->groupBy('a.gender')
            ->addOrderBy('counter', 'DESC')
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function countAllocationsByParams(array $param): string
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)');

        if (isset($param['gender'])) {
            if ('male' === $param['gender']) {
                $gender = 'M';
            } elseif ('female' === $param['gender']) {
                $gender = 'W';
            } else {
                $gender = 'D';
            }

            $qb->andWhere('a.gender = :gender')
                ->setParameter('gender', $gender);
        }

        if (isset($param['age'])) {
            $qb->andWhere('a.age = :age')
                ->setParameter('age', $param['age']);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function deleteByImport(Import $import = null): mixed
    {
        $qb = $this->createQueryBuilder('a')
            ->delete('App:Allocation', 'a')
            ->where('a.import = :import')
            ->setParameter(':import', $import->getId());

        return $qb->getQuery()->getResult();
    }
}
