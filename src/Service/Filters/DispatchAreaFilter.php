<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Domain\Entity\Hospital;
use App\Form\Filters\LocationType;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class DispatchAreaFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'dispatchArea';

    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getValue(Request $request): mixed
    {
        $area = $request->query->get('dispatchArea');

        if (empty($area)) {
            $value = null;
        } else {
            $value = urldecode($area);
        }

        return $this->setCacheValue($value);
    }

    public function supportsForm(): bool
    {
        return true;
    }

    public function buildForm(array $arguments): ?FormInterface
    {
        $form = $this->formFactory->create(LocationType::class, null, [
            'action' => $arguments['action'],
            'method' => $arguments['method'],
        ]);

        return $this->addHiddenFields($arguments['hidden'], $form);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $area = $this->cacheValue ?? $this->getValue($request);

        if (!isset($area)) {
            return $qb;
        }

        $qb->leftJoin($arguments[FilterService::ENTITY_ALIAS].'dispatchArea', 'dispatchArea')
            ->orWhere('dispatchArea.id = :dispatchArea')
                ->setParameter('dispatchArea', $area)
            ;

        return $qb;
    }
}
