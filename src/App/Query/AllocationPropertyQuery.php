<?php

declare(strict_types=1);

namespace App\Query;

use App\Application\Contract\ResultCollectionInterface;
use App\DataTransferObjects\ResultCollection;
use App\Entity\Allocation;
use App\Entity\Hospital;
use Doctrine\ORM\EntityManagerInterface;

final class AllocationPropertyQuery
{
    private ?Hospital $hospital = null;

    private string $property;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function execute(): ResultCollectionInterface
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(
            $this->property.' AS property',
            'COUNT('.$this->property.') AS counter'
        )
                ->from(Allocation::class, 'allocation')
                ->groupBy($this->property)
                ->addOrderBy($this->property, \Doctrine\Common\Collections\Criteria::DESC);

        if (null !== $this->hospital) {
            $qb->where('allocation.hospital = :hospital')
                ->setParameter(':hospital', $this->hospital);
        }

        $result = $qb->getQuery()->getResult();

        return new ResultCollection($result);
    }

    public function setTargetProoperty(string $property): void
    {
        $this->property = 'allocation.'.$property;
    }

    public function filterByHospital(Hospital $hospital): void
    {
        $this->hospital = $hospital;
    }
}
