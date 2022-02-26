<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Entity\User;
use App\Repository\HospitalRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class HospitalFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'hospital';

    private HospitalRepository $hospitalRepository;

    private Security $security;

    public function __construct(HospitalRepository $hospitalRepository, Security $security)
    {
        $this->hospitalRepository = $hospitalRepository;
        $this->security = $security;
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

        // If User is not an Admin deny access unless user is Owner of hospital
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            /** @var User $user */
            $user = $this->security->getUser();
            $denyAccess = true;

            foreach ($user->getHospitals() as $entity) {
                if ($entity->getId() === $hospital) {
                    $denyAccess = false;
                }
            }

            if ($denyAccess) {
                return $qb;
            }
        }

        return $qb->orWhere($arguments[FilterService::ENTITY_ALIAS].'hospital = :hospital')
            ->setParameter('hospital', $hospital)
            ;
    }
}
