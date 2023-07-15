<?php

declare(strict_types=1);

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Domain\Enum\HospitalTier;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class TierFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'tier';

    public function getValue(Request $request): mixed
    {
        $location = $request->query->get('tier');

        if (empty($location)) {
            $value = null;
        } else {
            $value = urldecode($location);
        }

        if (!in_array($value, HospitalTier::getValues(), true)) {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $location = $this->cacheValue ?? $this->getValue($request);

        if (!isset($location)) {
            return $qb;
        }

        $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'tier = :tier')
                ->setParameter('tier', $location)
        ;

        return $qb;
    }
}
