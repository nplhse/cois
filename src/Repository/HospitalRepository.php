<?php

namespace App\Repository;

use App\Entity\Hospital;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hospital|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hospital|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hospital[]    findAll()
 * @method Hospital[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HospitalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hospital::class);
    }

    public function findOneByUser(User $user): ?Hospital
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.owner = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countHospitals(): string
    {
        $qb = $this->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }
}
