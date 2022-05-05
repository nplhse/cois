<?php

namespace App\Query;

use App\Application\Contract\ResultCollectionInterface;
use App\DataTransferObjects\ResultCollection;
use App\Entity\Allocation;
use App\Service\FilterService;
use Doctrine\ORM\EntityManagerInterface;

final class AllocationUrgencyQuery
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(?FilterService $filterService = null): ResultCollectionInterface
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(
                'allocation.urgency AS urgency',
                'COUNT(allocation.urgency) AS counter'
            )
                ->where('allocation.urgency IS NOT NULL')
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.urgency')
                ->addOrderBy('allocation.urgency', \Doctrine\Common\Collections\Criteria::ASC);

        if (null !== $filterService) {
            $qb = $filterService->processQuery($qb, [FilterService::ENTITY_ALIAS => 'allocation.']);
        }

        return new ResultCollection($qb->getQuery()->getResult());
    }
}
