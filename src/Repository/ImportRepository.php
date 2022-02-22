<?php

namespace App\Repository;

use App\Domain\Contracts\ImportInterface;
use App\Domain\Repository\ImportRepositoryInterface;
use App\Entity\Import;
use App\Entity\User;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\FilterService;
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
    public const PAGINATOR_PER_PAGE = 20;

    public const PER_PAGE = 20;

    public const DEFAULT_ORDER = 'desc';

    public const DEFAULT_SORT = 'createdAt';

    public const SORTABLE = ['name', 'createdAt'];

    public const SEARCHABLE = ['name'];

    public const ENTITY_ALIAS = 'i.';

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

    public function getImportPaginator(FilterService $filterService): Paginator
    {
        $qb = $this->createQueryBuilder('i');

        $arguments = [
            'joinOwner' => true,
            PageFilter::PER_PAGE => self::PER_PAGE,
            FilterService::ENTITY_ALIAS => self::ENTITY_ALIAS,
            OrderFilter::DEFAULT_ORDER => self::DEFAULT_ORDER,
            OrderFilter::DEFAULT_SORT => self::DEFAULT_SORT,
            OrderFilter::SORTABLE => self::SORTABLE,
            SearchFilter::SEARCHABLE => self::SEARCHABLE,
        ];

        $qb = $filterService->processQuery($qb, $arguments);

        return new Paginator($qb->getQuery());
    }
}
