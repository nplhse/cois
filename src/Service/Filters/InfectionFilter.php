<?php

declare(strict_types=1);

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class InfectionFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'infection';

    public function getValue(Request $request): mixed
    {
        $infection = $request->query->get('infection');

        if (empty($infection)) {
            $value = null;
        } else {
            $value = urldecode($infection);
        }

        return $this->setCacheValue($value);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $infection = $this->cacheValue ?? $this->getValue($request);

        if (!isset($infection)) {
            return $qb;
        }

        $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'isInfectious = :isInfectious')
                ->setParameter('isInfectious', $infection)
        ;

        return $qb;
    }
}
