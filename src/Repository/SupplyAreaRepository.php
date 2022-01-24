<?php

namespace App\Repository;

use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Repository\SupplyAreaRepositoryInterface;
use App\Entity\SupplyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SupplyArea|null find($id, $lockMode = null, $lockVersion = null)
 * @method SupplyArea|null findOneBy(array $criteria, array $orderBy = null)
 * @method SupplyArea[]    findAll()
 * @method SupplyArea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupplyAreaRepository extends ServiceEntityRepository implements SupplyAreaRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SupplyArea::class);
    }

    public function add(SupplyAreaInterface $area): void
    {
        $this->_em->persist($area);
        $this->_em->flush();
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    public function delete(SupplyAreaInterface $area): void
    {
        $this->_em->remove($area);
        $this->_em->flush();
    }

    public function getById(int $id): SupplyAreaInterface
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getSingleResult();
    }
}
