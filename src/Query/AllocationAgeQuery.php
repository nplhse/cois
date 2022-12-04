<?php

namespace App\Query;

use App\Application\Contract\ResultCollectionInterface;
use App\DataTransferObjects\ResultCollection;
use App\Entity\Allocation;
use App\Entity\Hospital;
use Doctrine\ORM\EntityManagerInterface;

final class AllocationAgeQuery
{
    private ?Hospital $hospital = null;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function execute(): ResultCollectionInterface
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(
            'allocation.age AS age',
            'allocation.gender AS gender',
            'COUNT(allocation.age) AS counter'
        )
                ->from(Allocation::class, 'allocation')
                ->groupBy('allocation.age, allocation.gender')
                ->addOrderBy('allocation.age', \Doctrine\Common\Collections\Criteria::ASC);

        if (null !== $this->hospital) {
            $qb->where('allocation.hospital = :hospital')
                ->setParameter(':hospital', $this->hospital);
        }

        $result = $qb->getQuery()->getResult();

        return new ResultCollection($result);
    }

    public function filterByHospital(Hospital $hospital): void
    {
        $this->hospital = $hospital;
    }
}
