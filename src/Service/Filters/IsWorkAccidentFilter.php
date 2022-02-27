<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class IsWorkAccidentFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'isWorkAccident';

    public function getValue(Request $request): mixed
    {
        $isWorkAccident = $request->query->get('isWorkAccident');

        if (empty($isWorkAccident)) {
            $value = null;
        }

        if (1 === (int) $isWorkAccident) {
            $value = true;
        } else {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): mixed
    {
        return ['1' => 'Is Work Accident'];
    }

    public function getType(): string
    {
        return 'boolean';
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $isWorkAccident = $this->cacheValue ?? $this->getValue($request);

        if (!isset($isWorkAccident)) {
            return $qb;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'isWorkAccident = :isWorkAccident')
            ->setParameter('isWorkAccident', $isWorkAccident)
        ;
    }
}
