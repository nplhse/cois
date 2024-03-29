<?php

declare(strict_types=1);

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class RequiresCathlabFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'requiresCathlab';

    public function getValue(Request $request): mixed
    {
        $requiresCathlab = $request->query->get('requiresCathlab');

        if (empty($requiresCathlab)) {
            $value = null;
        }

        if (1 === (int) $requiresCathlab) {
            $value = true;
        } else {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): mixed
    {
        return ['1' => 'Requires Cathlab'];
    }

    public function getType(): string
    {
        return 'boolean';
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $requiresCathlab = $this->cacheValue ?? $this->getValue($request);

        if (!isset($requiresCathlab)) {
            return $qb;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'requiresCathlab = :requiresCathlab')
            ->setParameter('requiresCathlab', $requiresCathlab)
        ;
    }
}
