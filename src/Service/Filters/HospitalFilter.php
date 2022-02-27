<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Entity\User;
use App\Repository\HospitalRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\ItemInterface;

class HospitalFilter implements FilterInterface
{
    use FilterTrait;

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

    public function getAltValues(): array
    {
        return (new FilesystemAdapter())->get('hospital_filter', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $qb = $this->hospitalRepository->createQueryBuilder('h');
            $result = $qb->select('h.id, h.name')
                ->orderBy('h.id')
                ->getQuery()
                ->getArrayResult();

            $values = [];

            foreach ($result as $row) {
                $values[$row['id']] = $row['name'];
            }

            return $values;
        });
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
