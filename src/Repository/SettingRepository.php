<?php

namespace App\Repository;

use App\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Setting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Setting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Setting[]    findAll()
 * @method Setting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Setting::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Setting $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Setting $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findAllSettings(): array
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.category', \Doctrine\Common\Collections\Criteria::ASC)
            ->orderBy('s.name', \Doctrine\Common\Collections\Criteria::ASC)
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function clearAllSettings(): mixed
    {
        $qb = $this->createQueryBuilder('s')
            ->delete('App:Setting', 's')
        ;

        return $qb->getQuery()->execute();
    }
}
