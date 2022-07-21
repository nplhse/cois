<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class SpecialityFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'speciality';

    public function getValue(Request $request): mixed
    {
        $speciality = $request->query->get('speciality');

        if (empty($speciality)) {
            $value = null;
        } else {
            $value = urldecode($speciality);
        }

        return $this->setCacheValue($value);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $speciality = $this->cacheValue ?? $this->getValue($request);

        if (!isset($speciality)) {
            return $qb;
        }

        $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'speciality = :speciality')
                ->setParameter('speciality', $speciality)
        ;

        return $qb;
    }
}
