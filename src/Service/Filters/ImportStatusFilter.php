<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Entity\Import;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class ImportStatusFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'status';

    public function getValue(Request $request): mixed
    {
        $status = $request->query->get('status');

        if (empty($status)) {
            $value = null;
        }

        $availableStatus = [
            Import::STATUS_SUCCESS, Import::STATUS_FAILURE, Import::STATUS_PENDING, Import::STATUS_INCOMPLETE,
        ];

        if (in_array($status, $availableStatus, true)) {
            $value = $status;
        } else {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $status = $this->cacheValue ?? $this->getValue($request);

        if (!isset($status)) {
            return $qb;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'status = :status')
            ->setParameter('status', $status)
            ;
    }
}
