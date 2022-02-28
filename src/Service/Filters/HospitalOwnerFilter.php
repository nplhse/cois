<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Repository\HospitalRepository;
use App\Repository\UserRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class HospitalOwnerFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'owner';

    private Security $security;

    private UserRepository $userRepository;

    private TagAwareCacheInterface $cache;

    public function __construct(Security $security, UserRepository $userRepository, TagAwareCacheInterface $appCache)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->cache = $appCache;
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

    public function getAltValues(): array
    {
        return $this->cache->get('user_filter', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $item->tag(['filter', 'user_filter']);

            $qb = $this->userRepository->createQueryBuilder('u');
            $result = $qb->select('u.id, u.username as name')
                ->orderBy('u.id')
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
        $owner = $this->cacheValue ?? $this->getValue($request);

        if (HospitalRepository::ENTITY_ALIAS !== $arguments[FilterService::ENTITY_ALIAS]) {
            return $this->processJoinQuery($qb, $arguments, $request);
        }

        if (!isset($owner)) {
            return $qb;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'owner = :owner')
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
                ->andWhere('hospital.owner = :owner')
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
