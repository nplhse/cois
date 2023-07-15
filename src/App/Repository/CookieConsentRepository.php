<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CookieConsent;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\FilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CookieConsent|null find($id, $lockMode = null, $lockVersion = null)
 * @method CookieConsent|null findOneBy(array $criteria, array $orderBy = null)
 * @method CookieConsent[]    findAll()
 * @method CookieConsent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CookieConsentRepository extends ServiceEntityRepository
{
    public const PER_PAGE = 25;

    public const DEFAULT_ORDER = 'desc';

    public const DEFAULT_SORT = 'createdAt';

    public const SORTABLE = ['ipAddress', 'createdAt'];

    public const SEARCHABLE = ['ipAddress'];

    public const ENTITY_ALIAS = 'c.';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CookieConsent::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CookieConsent $entity, bool $flush = true): void
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
    public function remove(CookieConsent $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getPaginator(FilterService $filterService): Paginator
    {
        $qb = $this->createQueryBuilder('c');

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

    public function delete(): mixed
    {
        $qb = $this->createQueryBuilder('c')
            ->delete('App:CookieConsent', 'c');

        return $qb->getQuery()->execute();
    }
}
