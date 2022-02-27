<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class SearchFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'search';

    public const SEARCHABLE = 'searchable';

    public function getValue(Request $request): mixed
    {
        $search = $request->query->get('search');

        if (empty($search)) {
            $value = null;
        } else {
            $value = urldecode($search);
        }

        return $this->setCacheValue($value);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $search = $this->cacheValue ?? $this->getValue($request);

        if (!isset($search)) {
            return $qb;
        }

        foreach ($arguments[self::SEARCHABLE] as $key) {
            $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].$key.' LIKE :search')
                ->setParameter('search', '%'.$search.'%')
            ;
        }

        return $qb;
    }
}
