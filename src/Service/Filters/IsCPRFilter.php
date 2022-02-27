<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class IsCPRFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'isCPR';

    public function getValue(Request $request): mixed
    {
        $isCPR = $request->query->get('isCPR');

        if (empty($isCPR)) {
            $value = null;
        }

        if (1 === (int) $isCPR) {
            $value = true;
        } else {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): mixed
    {
        return ['1' => 'Is CPR'];
    }

    public function getType(): string
    {
        return 'boolean';
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $isCPR = $this->cacheValue ?? $this->getValue($request);

        if (!isset($isCPR)) {
            return $qb;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'isCPR = :isCPR')
            ->setParameter('isCPR', $isCPR)
        ;
    }
}
