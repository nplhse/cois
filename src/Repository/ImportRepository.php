<?php

namespace App\Repository;

use App\Domain\Contracts\ImportInterface;
use App\Domain\Repository\ImportRepositoryInterface;
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
class ImportRepository extends ServiceEntityRepository implements ImportRepositoryInterface
{
    // TODO: Remove after refactoring
    public const PAGINATOR_PER_PAGE = 10;

    public const PER_PAGE = 10;

    public const DEFAULT_ORDER = 'asc';

    public const DEFAULT_SORT = 'name';

    public const SORTABLE = ['name', 'createdAt'];

    public const SEARCHABLE = ['name'];

    public const ENTITY_ALIAS = 'h.';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Import::class);
    }

    public function add(ImportInterface $import): void
    {
        $this->_em->persist($import);
        $this->_em->flush();
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    public function delete(ImportInterface $import): void
    {
        $this->_em->remove($import);
        $this->_em->flush();
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

        if ('user' == $filter['show']) {
            $query->andWhere('i.user = :user')
                ->setParameter(':user', $filter['user'])
                ;
        } elseif ('hospital' == $filter['show']) {
            $query->andWhere('i.hospital = :hospital')
                ->setParameter(':hospital', $filter['hospital'])
            ;
        } elseif ('all' == $filter['show']) {
            // do nothing
        }

        if (empty($filter['show'])) {
            $query->andWhere('i.user = :user')
                ->setParameter(':user', $filter['user'])
                ->orWhere('i.hospital = :hospital')
                ->setParameter(':hospital', $filter['hospital'])
            ;
        }

        if (isset($filter['sortBy'])) {
            $sortBy = match ($filter['sortBy']) {
                'status' => 'i.status',
                'caption' => 'i.caption',
                default => 'i.createdAt',
            };
        } else {
            $sortBy = 'i.createdAt';
        }

        if (isset($filter['orderBy'])) {
            if ('desc' === $filter['orderBy']) {
                $order = 'DESC';
            } elseif ('asc' === $filter['orderBy']) {
                $order = 'ASC';
            } else {
                $order = 'DESC';
            }
        } else {
            $order = 'DESC';
        }

        $query
            ->orderBy($sortBy, $order)
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }
}
