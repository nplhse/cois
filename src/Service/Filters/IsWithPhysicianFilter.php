<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class IsWithPhysicianFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'isWithPhysician';

    public function getValue(Request $request): mixed
    {
        $isWithPhysician = $request->query->get('isWithPhysician');

        if (empty($isWithPhysician)) {
            $value = null;
        }

        if (1 === (int) $isWithPhysician) {
            $value = true;
        } else {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): mixed
    {
        return ['1' => 'Is with Physician'];
    }

    public function getType(): string
    {
        return 'boolean';
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $isWithPhysician = $this->cacheValue ?? $this->getValue($request);

        if (!isset($isWithPhysician)) {
            return $qb;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'isWithPhysician = :isWithPhysician')
            ->setParameter('isWithPhysician', $isWithPhysician)
        ;
    }
}
