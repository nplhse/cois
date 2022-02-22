<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class IsVentilatedFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'isVentilated';

    public function getValue(Request $request): mixed
    {
        $isVentilated = $request->query->get('isVentilated');

        if (empty($isVentilated)) {
            $value = null;
        }

        if (1 === (int) $isVentilated) {
            $value = true;
        } else {
            $value = null;
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
        $isVentilated = $this->cacheValue ?? $this->getValue($request);

        if (!isset($isVentilated)) {
            return $qb;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'isVentilated = :isVentilated')
            ->setParameter('isVentilated', $isVentilated)
        ;
    }
}
