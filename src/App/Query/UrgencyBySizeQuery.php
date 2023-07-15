<?php

declare(strict_types=1);

namespace App\Query;

use App\Application\Contract\ResultCollectionInterface;
use App\DataTransferObjects\ResultCollection;
use App\Entity\Allocation;
use Doctrine\ORM\EntityManagerInterface;

final class UrgencyBySizeQuery
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function execute(): ResultCollectionInterface
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->from(Allocation::class, 'a')
            ->select('COUNT(a.id) AS value, a.urgency, h.size')
            ->innerJoin('a.hospital', 'h')
            ->groupBy('h.id')
            ->addGroupBy('h.size')
            ->addGroupBy('a.urgency')
            ->orderBy('h.size')
        ;

        return new ResultCollection($qb->getQuery()->getResult());
    }
}
