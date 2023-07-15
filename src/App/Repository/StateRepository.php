<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\State;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Domain\Contracts\StateInterface;
use Domain\Repository\StateRepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

/**
 * @method State|null find($id, $lockMode = null, $lockVersion = null)
 * @method State|null findOneBy(array $criteria, array $orderBy = null)
 * @method State[]    findAll()
 * @method State[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
#[AsAlias(id: StateRepositoryInterface::class, public: true)]
class StateRepository extends ServiceEntityRepository implements StateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, State::class);
    }

    public function add(StateInterface $state): void
    {
        $this->_em->persist($state);
        $this->_em->flush();
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    public function delete(StateInterface $state): void
    {
        $this->_em->remove($state);
        $this->_em->flush();
    }

    public function getById(int $id): StateInterface
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getSingleResult();
    }
}
