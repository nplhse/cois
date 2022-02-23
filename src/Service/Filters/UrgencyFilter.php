<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class UrgencyFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'urgency';

    public function getValue(Request $request): mixed
    {
        $urgency = $request->query->get('urgency');

        if (empty($urgency)) {
            $value = null;
        } else {
            $value = (int) urldecode($urgency);
        }

        if (0 === $value) {
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
        $urgency = $this->cacheValue ?? $this->getValue($request);

        if (!isset($urgency)) {
            return $qb;
        }

        $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'urgency = :urgency')
                ->setParameter('urgency', $urgency)
            ;

        return $qb;
    }
}