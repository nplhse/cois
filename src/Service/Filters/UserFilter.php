<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Repository\UserRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class UserFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'user';

    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
        private TagAwareCacheInterface $cache
    ) {
    }

    public function getValue(Request $request): mixed
    {
        $user = (int) $request->query->get('user');

        if (empty($user)) {
            $value = null;
        }

        if ($user > 0) {
            $value = $user;
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
        $user = $this->cacheValue ?? $this->getValue($request);

        if ($this->security->isGranted('ROLE_ADMIN')) {
            if (!isset($user)) {
                return $qb;
            }

            return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'user = :user')
                ->setParameter('user', $user)
            ;
        }

        if (!isset($user)) {
            return $qb->orWhere($arguments[FilterService::ENTITY_ALIAS].'user = :user')
                ->setParameter('user', $this->security->getUser())
            ;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'user = :user')
            ->setParameter('user', $this->security->getUser())
        ;
    }
}
