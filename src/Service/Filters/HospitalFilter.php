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

class HospitalFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'hospital';

    private HospitalRepository $hospitalRepository;

    public function __construct(HospitalRepository $hospitalRepository)
    {
        $this->hospitalRepository = $hospitalRepository;
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

    public function getAltValue(Request $request): mixed
    {
        $hospitalId = $this->cacheValue ?? $this->getValue($request);

        if (isset($hospitalId)) {
            $hospital = $this->hospitalRepository->findOneBy(['id' => $hospitalId]);

            return $hospital->getName();
        }

        return null;
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
        $hospital = $this->cacheValue ?? $this->getValue($request);

        if (!isset($hospital)) {
            return $qb;
        }

        return $qb->orWhere($arguments[FilterService::ENTITY_ALIAS].'hospital = :hospital')
            ->setParameter('hospital', $hospital)
            ;
    }
}
