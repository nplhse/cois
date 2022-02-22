<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AssignmentFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'assignment';

    public function getValue(Request $request): mixed
    {
        $assignment = $request->query->get('assignment');

        if (empty($assignment)) {
            $value = null;
        } else {
            $value = urldecode($assignment);
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
        $assignment = $this->cacheValue ?? $this->getValue($request);

        if (!isset($assignment)) {
            return $qb;
        }

        $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'assignment = :assignment')
                ->setParameter('assignment', $assignment)
            ;

        return $qb;
    }
}
