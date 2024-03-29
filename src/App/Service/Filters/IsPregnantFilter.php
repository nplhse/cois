<?php

declare(strict_types=1);

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class IsPregnantFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'isPregnant';

    public function getValue(Request $request): mixed
    {
        $isPregnant = $request->query->get('isPregnant');

        if (empty($isPregnant)) {
            $value = null;
        }

        if (1 === (int) $isPregnant) {
            $value = true;
        } else {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): mixed
    {
        return ['1' => 'Is Pregnant'];
    }

    public function getType(): string
    {
        return 'boolean';
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $isPregnant = $this->cacheValue ?? $this->getValue($request);

        if (!isset($isPregnant)) {
            return $qb;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'isPregnant = :isPregnant')
            ->setParameter('isPregnant', $isPregnant)
        ;
    }
}
