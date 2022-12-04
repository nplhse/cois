<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Repository\DispatchAreaRepositoryInterface;
use App\Entity\DispatchArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DispatchArea|null find($id, $lockMode = null, $lockVersion = null)
 * @method DispatchArea|null findOneBy(array $criteria, array $orderBy = null)
 * @method DispatchArea[]    findAll()
 * @method DispatchArea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DispatchAreaRepository extends ServiceEntityRepository implements DispatchAreaRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DispatchArea::class);
    }

    public function add(DispatchAreaInterface $area): void
    {
        $this->_em->persist($area);
        $this->_em->flush();
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    public function delete(DispatchAreaInterface $area): void
    {
        $this->_em->remove($area);
        $this->_em->flush();
    }

    public function getById(int $id): DispatchAreaInterface
    {
        $qb = $this->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getSingleResult();
    }
}
