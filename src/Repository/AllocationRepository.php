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

    public function countAllocationsByAge(array $param = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.age, COUNT(a.age) AS counter')
            ->groupBy('a.age')
            ->orderBy('a.age', 'ASC');

        if (isset($param['gender'])) {
            $qb->andWhere('a.gender = :gender')
                ->setParameter(':gender', $param['gender']);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAllocationsByGender(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.gender, COUNT(a.gender) AS counter')
            ->groupBy('a.gender')
            ->addOrderBy('a.gender', 'DESC')
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function countAllocationsByTime(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.arrivalHour, COUNT(a.arrivalHour) AS counter')
            ->groupBy('a.arrivalHour')
            ->addOrderBy('a.arrivalHour', 'DESC')
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function countAllocationsByWeekday(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.arrivalWeekday, COUNT(a.arrivalWeekday) AS counter')
            ->groupBy('a.arrivalWeekday')
            ->addOrderBy('a.arrivalWeekday', 'DESC')
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function countAllocationsByRMI(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.PZC, COUNT(a.PZC) AS counter')
            ->groupBy('a.PZC')
            ->addOrderBy('counter', 'DESC')
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function countAllocationsBySK(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.SK, COUNT(a.SK) AS counter')
            ->groupBy('a.SK')
            ->addOrderBy('counter', 'DESC')
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function getAllPCZTexts(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.PZCText, a.PZC')
            ->addOrderBy('a.PZCText', 'DESC')
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function countAllocationsBySpeciality(bool $detail = false): array
    {
        if ($detail) {
            $qb = $this->createQueryBuilder('a')
                ->select('a.specialityDetail, COUNT(a.specialityDetail) AS counter')
                ->groupBy('a.specialityDetail')
                ->orderBy('a.specialityDetail', 'ASC');
        } else {
            $qb = $this->createQueryBuilder('a')
                ->select('a.speciality, COUNT(a.speciality) AS counter')
                ->groupBy('a.speciality')
                ->orderBy('a.speciality', 'ASC');
        }

        return $qb->getQuery()->getResult();
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
