<?php

declare(strict_types=1);

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Entity\User;
use App\Repository\HospitalRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class HospitalFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'hospital';

    public function __construct(
        private HospitalRepository $hospitalRepository,
        private Security $security,
        private TagAwareCacheInterface $cache
    ) {
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
        return $this->cache->get('hospital_filter', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $item->tag(['filter', 'hospital_filter']);

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

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'hospital = :hospital')
            ->setParameter('hospital', $hospital);
    }
}
