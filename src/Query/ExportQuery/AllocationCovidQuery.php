<?php

namespace App\Query\ExportQuery;

use App\Application\Contract\ResultCollectionInterface;
use App\DataTransferObjects\ResultCollection;
use App\Entity\Allocation;
use App\Service\FilterService;
use Doctrine\ORM\EntityManagerInterface;

final class AllocationCovidQuery
{
    private const startYear = '2019';

    private const endYear = '2021';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function executeIndication(int $indicationCode, ?FilterService $filterService = null): ResultCollectionInterface
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(
                'allocation.indicationCode',
                'allocation.urgency',
                'COUNT(allocation.urgency) as count',
                'allocation.creationYear',
                'allocation.creationMonth',
            )
                ->where('allocation.indicationCode = :indicationCode')
                ->setParameter('indicationCode', $indicationCode)
                ->andWhere($qb->expr()->between('allocation.creationYear', self::startYear, self::endYear))
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.indicationCode')
                ->addGroupBy('allocation.urgency')
                ->addGroupBy('allocation.creationYear')
                ->addGroupBy('allocation.creationMonth')
                ->orderBy('allocation.creationYear')
                ->addOrderBy('allocation.creationMonth')
        ;

        if (null !== $filterService) {
            $qb = $filterService->processQuery($qb, [FilterService::ENTITY_ALIAS => 'allocation.']);
        }

        return new ResultCollection($qb->getQuery()->getResult());
    }

    public function executeStats(int $indicationCode, ?FilterService $filterService = null): ResultCollectionInterface
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(
            'allocation.indicationCode',
            'allocation.urgency',
            'COUNT(allocation.urgency) as count',
            'allocation.creationYear',
            'allocation.creationMonth',
        )
            ->where('allocation.indicationCode = :indicationCode')
            ->setParameter('indicationCode', $indicationCode)
            ->andWhere($qb->expr()->between('allocation.creationYear', self::startYear, self::endYear))
            ->from(Allocation::class, 'allocation')
            ->groupBy('allocation.indicationCode')
            ->addGroupBy('allocation.urgency')
            ->addGroupBy('allocation.creationYear')
            ->addGroupBy('allocation.creationMonth')
            ->orderBy('allocation.creationYear')
            ->addOrderBy('allocation.creationMonth')
        ;

        if (null !== $filterService) {
            $qb = $filterService->processQuery($qb, [FilterService::ENTITY_ALIAS => 'allocation.']);
        }

        return new ResultCollection($qb->getQuery()->getResult());
    }
}
