<?php

declare(strict_types=1);

namespace App\Query\Export;

use App\Entity\Allocation;
use App\Entity\Hospital;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class AllocationBySizeQuery
{
    private ?QueryBuilder $query = null;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function new(): self
    {
        $this->query = $this->entityManager->createQueryBuilder();
        $this->query->from(Allocation::class, 'a');

        return $this;
    }

    public function getResults(): array
    {
        return $this->query->getQuery()->getResult();
    }

    public function findAllocationsBySize(): self
    {
        $this->query
            ->select('COUNT(a.id) as value, h.size')
            ->innerJoin(Hospital::class, 'h')
            ->groupBy('h.size')
            ->orderBy('h.size', 'ASC');

        return $this;
    }

    public function findAllocationsByTier(): self
    {
        $this->query
            ->select('COUNT(a.id) as value, h.tier, HOUR(a.createdAt) as hour, a.age, a.urgency, a.isWithPhysician')
            ->innerJoin(Hospital::class, 'h')
            ->groupBy('h.tier')
            ->orderBy('h.tier', 'ASC');

        return $this;
    }

    public function findByLocation(): self
    {
        $this->query
            ->addSelect('h.location')
            ->addGroupBy('h.location')
            ->orderBy('h.location', 'ASC');

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

    public function filterByTracer(string $tracer): self
    {
        $where = match ($tracer) {
            'cpr' => [124, 130],
            'pulmonary_embolism' => [349],
            'acs_stemi' => [331, 332, 333],
            'pneumonia_copd' => [312, 315],
            'stroke' => [421, 422, 423],
            default => [],
        };

        $query = '';

        for ($i = 0, $iMax = count($where); $i < $iMax; ++$i) {
            if (0 == $i) {
                $query = 'a.indicationCode = '.$where[$i];
                continue;
            }

            $query .= ' OR a.indicationCode = '.$where[$i];
        }

        $this->query->andWhere($query);

        return $this;
    }
}
