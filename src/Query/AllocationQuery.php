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
        } elseif ('pzc' === $this->property) {
            $qb->select(
                'allocation.RMI AS PZC',
                'COUNT(allocation.RMI) AS counter',
                'allocation.PZCText AS PZCText'
            )
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.RMI, allocation.PZCText')
                ->addOrderBy('counter', 'DESC');
        } elseif ('speciality' === $this->property) {
            $qb->select(
                'allocation.speciality AS speciality',
                'COUNT(allocation.speciality) AS counter',
            )
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.speciality')
                ->addOrderBy('counter', 'DESC');
        } elseif ('specialityDetail' === $this->property) {
            $qb->select(
                'allocation.specialityDetail AS speciality',
                'COUNT(allocation.speciality) AS counter',
            )
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.specialityDetail')
                ->addOrderBy('counter', 'DESC');
        } elseif ('infection' === $this->property) {
            $qb->select(
                'allocation.isInfectious AS infection',
                'COUNT(allocation.isInfectious) AS counter',
            )
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.isInfectious')
                ->addOrderBy('counter', 'DESC');
        } elseif ('assignment' === $this->property) {
            $qb->select(
                'allocation.assignment AS assignment',
                'COUNT(allocation.assignment) AS counter',
            )
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.assignment')
                ->addOrderBy('counter', 'DESC');
        } elseif ('occasion' === $this->property) {
            $qb->select(
                'allocation.occasion AS occasion',
                'COUNT(allocation.occasion) AS counter',
            )
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.occasion')
                ->addOrderBy('counter', 'DESC');
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
