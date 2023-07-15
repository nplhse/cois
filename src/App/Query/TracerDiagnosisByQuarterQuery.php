<?php

declare(strict_types=1);

namespace App\Query;

use App\Application\Contract\ResultCollectionInterface;
use App\DataTransferObjects\ResultCollection;
use App\Entity\Allocation;
use Doctrine\ORM\EntityManagerInterface;

final class TracerDiagnosisByQuarterQuery
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function execute(string $cluster): ResultCollectionInterface
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->from(Allocation::class, 'a')
            ->select('COUNT(a.id) AS value, QUARTER(a.createdAt) as quarter, YEAR(a.createdAt) as year')
            ->where('a.createdAt BETWEEN 2019-01-01 AND 2022-12-31')
            ->groupBy('year')
            ->addGroupBy('quarter')
        ;

        switch ($cluster) {
            case 'total':
                break;
            case 'cpr':
                $qb->andWhere('a.indicationCode = 124')
                    ->orWhere('a.indicationCode = 130');
                break;
            case 'pulmonary_embolism':
                $qb->andWhere('a.indicationCode = 349');
                break;
            case 'pneumonia_copd':
                $qb->andWhere('a.indicationCode = 312')
                    ->orWhere('a.indicationCode = 315');
                break;
            case 'stemi_acs':
                $qb->andWhere('a.indicationCode = 331')
                    ->orWhere('a.indicationCode = 332')
                    ->orWhere('a.indicationCode = 333');
                break;
            case 'stroke':
                $qb->andWhere('a.indicationCode = 421')
                    ->orWhere('a.indicationCode = 422')
                    ->orWhere('a.indicationCode = 423');
                break;
        }

        return new ResultCollection($qb->getQuery()->getResult());
    }
}
