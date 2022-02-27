<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RequiresResusFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'requiresResus';

    public function getValue(Request $request): mixed
    {
        $requiresResus = $request->query->get('requiresResus');

        if (empty($requiresResus)) {
            $value = null;
        }

        if (1 === (int) $requiresResus) {
            $value = true;
        } else {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): mixed
    {
        return ['1' => 'Requires Resuscitation'];
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
        $requiresResus = $this->cacheValue ?? $this->getValue($request);

        if (!isset($requiresResus)) {
            return $qb;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'requiresResus = :requiresResus')
            ->setParameter('requiresResus', $requiresResus)
        ;
    }
}
