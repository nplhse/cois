<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SpecialityDetailFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'specialityDetail';

    public function getValue(Request $request): mixed
    {
        $specialityDetail = $request->query->get('specialityDetail');

        if (empty($specialityDetail)) {
            $value = null;
        } else {
            $value = urldecode($specialityDetail);
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
        $specialityDetail = $this->cacheValue ?? $this->getValue($request);

        if (!isset($specialityDetail)) {
            return $qb;
        }

        $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'specialityDetail = :specialityDetail')
                ->setParameter('specialityDetail', $specialityDetail)
            ;

        return $qb;
    }
}
