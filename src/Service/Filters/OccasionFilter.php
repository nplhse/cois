<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class OccasionFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'occasion';

    public function getValue(Request $request): mixed
    {
        $occasion = $request->query->get('occasion');

        if (empty($occasion)) {
            $value = null;
        } else {
            $value = urldecode($occasion);
        }

        return $this->setCacheValue($value);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $occasion = $this->cacheValue ?? $this->getValue($request);

        if (!isset($occasion)) {
            return $qb;
        }

        $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'occasion = :occasion')
                ->setParameter('occasion', $occasion)
        ;

        return $qb;
    }
}
