<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class IsShockFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'isShock';

    public function getValue(Request $request): mixed
    {
        $isShock = $request->query->get('isShock');

        if (empty($isShock)) {
            $value = null;
        }

        if (1 === (int) $isShock) {
            $value = true;
        } else {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): mixed
    {
        return ['1' => 'Is Shock'];
    }

    public function getType(): string
    {
        return 'boolean';
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
        $isShock = $this->cacheValue ?? $this->getValue($request);

        if (!isset($isShock)) {
            return $qb;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'isShock = :isShock')
            ->setParameter('isShock', $isShock)
        ;
    }
}
