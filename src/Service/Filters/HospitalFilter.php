<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Form\Filters\HospitalFilterType;
use App\Repository\HospitalRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class HospitalFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'hospital';

    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getValue(Request $request): mixed
    {
        $hospital = (int) $request->query->get('hospital');

        if (empty($hospital)) {
            $value = null;
        }

        if ($hospital > 0) {
            $value = $hospital;
        } else {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function supportsForm(): bool
    {
        return true;
    }

    public function buildForm(array $arguments): ?FormInterface
    {
        $form = $this->formFactory->create(HospitalFilterType::class, null, [
            'action' => $arguments['action'],
            'method' => $arguments['method'],
        ]);

        return $this->addHiddenFields($arguments['hidden'], $form);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        if (HospitalRepository::ENTITY_ALIAS !== $arguments[FilterService::ENTITY_ALIAS]) {
            $hospital = $this->cacheValue ?? $this->getValue($request);

            if (!isset($hospital)) {
                return $qb;
            }

            return $qb->orWhere($arguments[FilterService::ENTITY_ALIAS].'hospital = :hospital')
                ->setParameter('hospital', $hospital)
                ;
        }

        return $qb;
    }
}
