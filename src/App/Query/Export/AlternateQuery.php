<?php

declare(strict_types=1);

namespace App\Query\Export;

use App\Entity\Allocation;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class AlternateQuery
{
    private ?QueryBuilder $query = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
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
        return $this->query->getQuery()->getArrayResult();
    }

    public function sumTotalAllocationsByQuarter(): array
    {
        return $this->sumAllocationsByQuarter();
    }

    public function sumAllocationsByQuarter(?string $tracer = null): array
    {
        $conn = $this->entityManager->getConnection();

        $where = match ($tracer) {
            'cpr' => [124, 130],
            'pulmonary_embolism' => [349],
            'acs_stemi' => [331, 332, 333],
            'pneumonia_copd' => [312, 315],
            'stroke' => [421, 422, 423],
            default => null,
        };

        $query = 'WHERE a.created_at BETWEEN :from AND :to';

        if (null !== $where) {
            for ($i = 0, $iMax = count($where); $i < $iMax; ++$i) {
                if (0 === $i) {
                    $query .= ' AND (a.indication_code = '.$where[$i];
                    continue;
                }

                $query .= ' OR a.indication_code = '.$where[$i];
            }

            $query .= ')';
        }

        $sql = 'SELECT COUNT(a.id) as value, YEAR(a.created_at) as year, QUARTER(a.created_at) as quarter 
            FROM allocation AS a '.$query.' GROUP BY year, quarter 
            ORDER BY year, quarter ASC';

        $sql = trim(preg_replace('/\s\s+/', ' ', $sql));

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('from', '2019-01-01 00:00:00');
        $stmt->bindValue('to', '2022-12-31 23:59:59');

        /*
         * @psalm-suppress UndefinedInterfaceMethod
         */
        return $stmt->executeQuery()->fetchAllAssociative();
    }

    public function findOLDAllocationsByQuarter(): self
    {
        $from = new \DateTimeImmutable('2019-01-01 00:00:00');
        $to = new \DateTimeImmutable('2022-12-31 23:59:59');

        $this->query
            ->select('COUNT(a.id) as value, YEAR(a.createdAt) as year, QUARTER(a.createdAt) as quarter')
            ->andWhere('a.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to', $to, Types::DATETIME_IMMUTABLE)
            ->orderBy('year', 'ASC')
            ->addOrderBy('quarter', 'ASC');

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
