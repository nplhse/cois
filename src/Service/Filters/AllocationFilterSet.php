<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Form\Filters\AllocationFilterSetType;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AllocationFilterSet implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'allocation-set';

    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getValue(Request $request): mixed
    {
        return null;
    }

    public function supportsForm(): bool
    {
        return true;
    }

    public function buildForm(array $arguments): ?FormInterface
    {
        $form = $this->formFactory->create(AllocationFilterSetType::class, null, [
            'action' => $arguments['action'],
            'method' => $arguments['method'],
        ]);

        return $this->addHiddenFields($arguments['hidden'], $form);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        return $qb;
    }
}
