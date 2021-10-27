<?php

namespace App\Query;

use App\DataTransferObjects\ResultCollection;
use App\DataTransferObjects\ResultCollectionInterface;
use App\Entity\Allocation;
use App\Entity\Hospital;
use Doctrine\ORM\EntityManagerInterface;

final class AllocationQuery
{
    private EntityManagerInterface $entityManager;

    private string $property;

    private ?Hospital $hospital = null;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(): ResultCollectionInterface
    {
        $qb = $this->entityManager->createQueryBuilder();

        if ('days' === $this->property) {
            $qb->select(
                'allocation.creationWeekday AS day',
                'COUNT(allocation.creationWeekday) AS counter'
            )
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.creationWeekday')
                ->addOrderBy('allocation.creationWeekday', 'DESC');
        } elseif ('gender' === $this->property) {
            $qb->select(
                    'allocation.gender',
                    'COUNT(allocation.gender) AS counter'
                )
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.gender')
                ->addOrderBy('allocation.gender', 'DESC');
        } elseif ('times' === $this->property) {
            $qb->select(
                'allocation.creationHour AS time',
                'COUNT(allocation.creationHour) AS counter'
            )
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.creationHour')
                ->addOrderBy('allocation.creationHour', 'ASC');
        } elseif ('urgency' === $this->property) {
            $qb->select(
                'allocation.SK AS urgency',
                'COUNT(allocation.SK) AS counter'
            )
                ->where('allocation.SK IS NOT NULL')
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.SK')
                ->addOrderBy('allocation.SK', 'ASC');
        }

        if (null !== $this->hospital) {
            $qb->where('allocation.hospital = :hospital')
                ->setParameter(':hospital', $this->hospital);
        }

        $allocation = $qb->getQuery()->getResult();

        return new ResultCollection($allocation);
    }

    public function filterByHospital(Hospital $hospital): void
    {
        $this->hospital = $hospital;
    }

    public function groupBy(string $property): void
    {
        $this->property = $property;
    }
}
