<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Application\Exception\FilterMissingArgumentException;
use App\Service\Filters\Traits\FilterTrait;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class PageFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'page';

    public const PER_PAGE = 'per_page';

    public function getValue(Request $request): mixed
    {
        $page = $request->query->get('page');

        if (is_numeric($page) && $page > 0) {
            $value = (int) $page;
        } else {
            $value = 1;
        }

        return $this->setCacheValue($value);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $page = $this->cacheValue ?? $this->getValue($request);

        $this->checkArguments($arguments, [self::PER_PAGE]);

        if (1 !== $page) {
            $offset = $page * $arguments[self::PER_PAGE];
        } else {
            $offset = 0;
        }

        return $qb
            ->setMaxResults($arguments[self::PER_PAGE])
            ->setFirstResult($offset);
    }
}
