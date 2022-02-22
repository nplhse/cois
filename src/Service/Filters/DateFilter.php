<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class DateFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'date';

    public function getValue(Request $request): mixed
    {
        $value = [];
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        if (empty($startDate)) {
            $value['startDate'] = null;
        } else {
            $value['startDate'] = urldecode($startDate);
        }

        if (empty($endDate)) {
            $value['endDate'] = null;
        } else {
            $value['endDate'] = urldecode($endDate);
        }

        return $this->setCacheValue($value);
    }

    public function supportsForm(): bool
    {
        return false;
    }

    public function buildForm(array $arguments): ?FormInterface
    {
        return null;
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $date = $this->cacheValue ?? $this->getValue($request);

        if (!isset($date)) {
            return $qb;
        }

        if ($date['startDate']) {
            $param = \DateTime::createFromFormat('Y-m-d G:i', $date['startDate'].'00:00');

            $qb->andWhere('a.createdAt >= :startDate')
                ->setParameter('startDate', $param, Types::DATETIME_MUTABLE)
            ;
        }

        if ($date['endDate']) {
            $param = \DateTime::createFromFormat('Y-m-d G:i', $date['endDate'].'00:00');

            $qb->andWhere('a.createdAt <= :endDate')
                ->setParameter('endDate', $param, Types::DATETIME_MUTABLE)
            ;
        }

        return $qb;
    }
}
