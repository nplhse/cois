<?php

declare(strict_types=1);

namespace App\Query\Export;

use App\Entity\Allocation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class DGINA23ExportQuery
{
    private ?QueryBuilder $query = null;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->query = $this->entityManager->createQueryBuilder();
        $this->query->from(Allocation::class, 'a');
    }

    public function getResults(): array
    {
        return $this->query->getQuery()->getResult();
    }

    public function findAllocationsByHour(): self
    {
        $this->query
            ->select('COUNT(a.id) as value, HOUR(a.createdAt) as hour')
            ->groupBy('hour')
            ->orderBy('hour', 'ASC');

        return $this;
    }

    public function findAllocationsByQuarter(): self
    {
        $this->query
            ->select('COUNT(a.id) as value, YEAR(a.createdAt) as year, QUARTER(a.createdAt) as quarter')
            ->groupBy('year')
            ->groupBy('quarter')
            ->orderBy('year', 'ASC')
            ->orderBy('quarter', 'ASC');

        return $this;
    }

    public function filterBySpeciality(string $speciality): self
    {
        $this->query->andWhere('a.speciality = :speciality')->setParameter('speciality', $speciality);

        return $this;
    }

    public function filterBySpecialityDetail(string $specialityDetail): self
    {
        $this->query->andWhere('a.specialityDetail = :specialityDetail')->setParameter('specialityDetail', $specialityDetail);

        return $this;
    }

    public function filterByProperty(string $property): self
    {
        $where = match ($property) {
            'resus' => 'a.requiresResus',
            'cathlab' => 'a.requiresCathlab',
            default => '',
        };

        $this->query->andWhere($where.' = :value')->setParameter('value', true);

        return $this;
    }
}
