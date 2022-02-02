<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class OrderFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'order';

    public const VALID_ORDER = ['asc', 'desc'];

    public const DEFAULT_ORDER = 'default_order';

    public const DEFAULT_SORT = 'default_sort';

    public const SORTABLE = 'sortable';

    public function getValue(Request $request): mixed
    {
        $value = [];

        $orderBy = $request->query->get('orderBy');

        if (in_array($orderBy, self::VALID_ORDER, true)) {
            $value['orderBy'] = $orderBy;
        } else {
            $value['orderBy'] = null;
        }

        $sortBy = $request->query->get('sortBy');

        if (empty($sortBy)) {
            $value['sortBy'] = null;
        } else {
            $value['sortBy'] = urlencode($sortBy);
        }

        return $this->setCacheValue($value);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $order = $this->cacheValue ?? $this->getValue($request);

        $this->checkArguments($arguments, [self::DEFAULT_ORDER, self::DEFAULT_SORT, self::SORTABLE]);

        $orderBy = $order['orderBy'];
        $sortBy = $order['sortBy'];

        if (!isset($orderBy)) {
            $orderBy = $arguments[self::DEFAULT_ORDER];
        }

        if (!isset($sortBy)) {
            $sortBy = $arguments[FilterService::ENTITY_ALIAS].$arguments[self::DEFAULT_SORT];
        }

        if (in_array($sortBy, $arguments[self::SORTABLE], true)) {
            $sortBy = $arguments[FilterService::ENTITY_ALIAS].$sortBy;
        } else {
            $sortBy = $arguments[FilterService::ENTITY_ALIAS].$arguments[self::DEFAULT_SORT];
        }

        return $qb->orderBy($sortBy, $orderBy);
    }
}
