<?php

declare(strict_types=1);

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Domain\Enum\HospitalSize;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class SizeFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'size';

    public function getValue(Request $request): mixed
    {
        $size = $request->query->get('size');

        if (empty($size)) {
            $value = null;
        } else {
            $value = urldecode($size);
        }

        if (!in_array($value, HospitalSize::getValues(), true)) {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $size = $this->cacheValue ?? $this->getValue($request);

        if (!isset($size)) {
            return $qb;
        }

        $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'size = :size')
                ->setParameter('size', $size)
        ;

        return $qb;
    }
}
