<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Form\Filters\HospitalFilterType;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class HospitalFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'hospital';

    private FormFactoryInterface $formFactory;

    private Security $security;

    public function __construct(FormFactoryInterface $formFactory, Security $security)
    {
        $this->formFactory = $formFactory;
        $this->security = $security;
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
        $form = $this->formFactory->create(HospitalFilterType::class, null, [
            'action' => $arguments['action'],
            'method' => $arguments['method'],
        ]);

        return $this->addHiddenFields($arguments['hidden'], $form);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $ownHospitals = $this->cacheValue ?? $this->getValue($request);

        if (!isset($ownHospitals)) {
            return $qb;
        }

        return $qb->orWhere($arguments[FilterService::ENTITY_ALIAS].'owner = :owner')
            ->setParameter('owner', $this->security->getUser())
        ;
    }
}
