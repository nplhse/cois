<?php

namespace App\Repository;

use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\UserInterface;
use App\Domain\Repository\HospitalRepositoryInterface;
use App\Entity\Hospital;
use App\Entity\User;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\FilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hospital|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hospital|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hospital[]    findAll()
 * @method Hospital[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HospitalRepository extends ServiceEntityRepository implements HospitalRepositoryInterface
{
    public const PER_PAGE = 10;

    public const DEFAULT_ORDER = 'asc';

    public const DEFAULT_SORT = 'name';

    public const SORTABLE = ['name', 'createdAt'];

    public const SEARCHABLE = ['name'];

    public const ENTITY_ALIAS = 'h.';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hospital::class);
    }

    public function add(HospitalInterface $hospital): void
    {
        $this->_em->persist($hospital);
        $this->_em->flush();
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    public function delete(HospitalInterface $hospital): void
    {
        $this->_em->remove($hospital);
        $this->_em->flush();
    }

    public function findOneByTriplet(string $name, string $location, int $beds): ?Hospital
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.name = :name')
            ->setParameter(':name', $name)
            ->andWhere('h.location = :location')
            ->setParameter(':location', $location)
            ->andWhere('h.beds = :beds')
            ->setParameter(':beds', $beds)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByUser(User $user): ?Hospital
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.owner = :user')
            ->setParameter(':user', $user->getId())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneById(int $id): ?Hospital
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.id = :id')
            ->setParameter(':id', $id)
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

    public function countHospitalsByUsers(UserInterface $user): string
    {
        $qb = $this->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->andWhere('h.owner = :user')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }

    public function getHospitals(): array
    {
        return $this->createQueryBuilder('h')
            ->select('h.name, h.id')
            ->distinct(true)
            ->orderBy('h.name', \Doctrine\Common\Collections\Criteria::ASC)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function getHospitalPaginator(FilterService $filterService): Paginator
    {
        $qb = $this->createQueryBuilder('h');

        $arguments = [
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
