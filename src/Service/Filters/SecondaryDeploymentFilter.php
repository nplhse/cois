<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class SecondaryDeploymentFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'secondaryDeployment';

    public function getValue(Request $request): mixed
    {
        $secondaryDeployment = $request->query->get('secondaryDeployment');

        if (empty($secondaryDeployment)) {
            $value = null;
        } else {
            $value = urldecode($secondaryDeployment);
        }

        return $this->setCacheValue($value);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $secondaryDeployment = $this->cacheValue ?? $this->getValue($request);

        if (!isset($secondaryDeployment)) {
            return $qb;
        }

        if ('None' === $secondaryDeployment) {
            return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'secondaryDeployment IS NULL')
            ;
        }

        $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'secondaryDeployment = :secondaryDeployment')
                ->setParameter('secondaryDeployment', $secondaryDeployment)
        ;

        return $qb;
    }
}
