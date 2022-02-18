<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class HospitalOwnerFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'hospital-owner';

    public function getValue(Request $request): mixed
    {
        $owner = (int) $request->query->get('owner');

        if (empty($owner)) {
            $value = null;
        }

        if ($owner > 0) {
            $value = $owner;
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
        $owner = $this->cacheValue ?? $this->getValue($request);

        if (!isset($owner)) {
            return $qb;
        }

        return $qb->orWhere($arguments[FilterService::ENTITY_ALIAS].'owner = :owner')
            ->setParameter('owner', $owner)
        ;
    }
}
