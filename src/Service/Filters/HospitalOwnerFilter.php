<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Repository\HospitalRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class HospitalOwnerFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'hospital-owner';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

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

        if (HospitalRepository::ENTITY_ALIAS !== $arguments[FilterService::ENTITY_ALIAS]) {
            return $this->processJoinQuery($qb, $arguments, $request);
        }

        if (!isset($owner)) {
            return $qb;
        }

        return $qb->orWhere($arguments[FilterService::ENTITY_ALIAS].'owner = :owner')
            ->setParameter('owner', $owner)
        ;
    }

    public function processJoinQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $owner = $this->cacheValue ?? $this->getValue($request);

        if ($this->security->isGranted('ROLE_ADMIN')) {
            if (!isset($owner)) {
                return $qb;
            }

            return $qb->leftJoin($arguments[FilterService::ENTITY_ALIAS].'hospital', 'hospital')
                ->orWhere('hospital.owner = :owner')
                ->setParameter('owner', $owner)
                ;
        }

        if (!isset($owner)) {
            return $qb;
        }

        return $qb->leftJoin($arguments[FilterService::ENTITY_ALIAS].'hospital', 'hospital')
            ->andWhere('hospital.owner = :owner')
            ->setParameter('owner', $this->security->getUser());
    }
}
