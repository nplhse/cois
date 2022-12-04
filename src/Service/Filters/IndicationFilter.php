<?php

declare(strict_types=1);

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class IndicationFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'indication';

    public function getValue(Request $request): mixed
    {
        $indicationCode = $request->query->get('indication');

        if (empty($indicationCode)) {
            $value = null;
        } else {
            $value = (int) urldecode($indicationCode);
        }

        if (0 == $value) {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $indicationCode = $this->cacheValue ?? $this->getValue($request);

        if (!isset($indicationCode)) {
            return $qb;
        }

        $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'indicationCode = :indicationCode')
                ->setParameter('indicationCode', $indicationCode)
        ;

        return $qb;
    }
}
