<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Form\Filters\SearchType;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SearchFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'search';

    public const SEARCHABLE = 'searchable';

    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getValue(Request $request): mixed
    {
        $search = $request->query->get('search');

        if (empty($search)) {
            $value = null;
        } else {
            $value = urldecode($search);
        }

        return $this->setCacheValue($value);
    }

    public function supportsForm(): bool
    {
        return true;
    }

    public function buildForm(array $arguments): ?FormInterface
    {
        $form = $this->formFactory->create(SearchType::class, null, [
            'action' => $arguments['action'],
            'method' => $arguments['method'],
        ]);

        return $this->addHiddenFields($arguments['hidden'], $form);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $search = $this->cacheValue ?? $this->getValue($request);

        $this->checkArguments($arguments, [self::SEARCHABLE]);

        if (!isset($search)) {
            return $qb;
        }

        foreach ($arguments[self::SEARCHABLE] as $key) {
            $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].$key.' LIKE :search')
                ->setParameter('search', '%'.$search.'%')
            ;
        }

        return $qb;
    }
}
