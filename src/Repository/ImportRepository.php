<?php

namespace App\Repository;

use App\Entity\Import;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Import|null find($id, $lockMode = null, $lockVersion = null)
 * @method Import|null findOneBy(array $criteria, array $orderBy = null)
 * @method Import[]    findAll()
 * @method Import[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Import::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.user = :val')
            ->setParameter('val', $user->getId())
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function countImports(User $user = null): mixed
    {
        if ($user) {
            $qb = $this->createQueryBuilder('i')
                ->select('COUNT(i.id)')
                ->andWhere('i.user = :user')
                ->setparameter('user', $user->getId())
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            $qb = $this->createQueryBuilder('i')
                ->select('COUNT(i.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }

        return $qb;
    }

    public function getAllImports(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    public function getImportPaginator(int $page, array $filter): Paginator
    {
        if (1 != $page) {
            $offset = $page * self::PAGINATOR_PER_PAGE;
        } else {
            $offset = 0;
        }

        $query = $this->createQueryBuilder('i');

        if ($filter['search']) {
            $query->where('i.caption LIKE :search')
                ->setParameter('search', '%'.$filter['search'].'%')
            ;
        }

        if ($filter['user']) {
            $query->andWhere('i.user = :user')
                ->setParameter('user', $filter['user'])
            ;
        }

        if ($filter['hospital']) {
            $query->orWhere('i.hospital = :hospital')
                ->setParameter('hospital', $filter['hospital'])
            ;
        }

        $query
            ->orderBy('i.createdAt', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }
}
