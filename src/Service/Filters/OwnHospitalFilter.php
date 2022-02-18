<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class OwnHospitalFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'own-hospital';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getValue(Request $request): mixed
    {
        $ownHospitals = $request->query->get('ownHospitals');

        if (empty($ownHospitals)) {
            $value = null;
        }

        if (1 === (int) $ownHospitals) {
            $value = 1;
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
        $ownHospitals = $this->cacheValue ?? $this->getValue($request);

        if (!isset($ownHospitals)) {
            return $qb;
        }

        return $qb->orWhere($arguments[FilterService::ENTITY_ALIAS].'owner = :owner')
            ->setParameter('owner', $this->security->getUser())
        ;
    }
}
