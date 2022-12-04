<?php

declare(strict_types=1);

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Repository\StateRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class StateFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'state';

    public function __construct(
        private StateRepository $stateRepository,
        private TagAwareCacheInterface $cache
    ) {
    }

    public function getValue(Request $request): mixed
    {
        $state = $request->query->get('state');

        if (empty($state)) {
            $value = null;
        } else {
            $value = (int) urldecode($state);
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): array
    {
        return $this->cache->get('state_filter', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $item->tag(['filter', 'state_filter']);

            $qb = $this->stateRepository->createQueryBuilder('s');
            $result = $qb->select('s.id, s.name')
                ->orderBy('s.id')
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
        $state = $this->cacheValue ?? $this->getValue($request);

        if (!isset($state)) {
            return $qb;
        }

        $qb->leftJoin($arguments[FilterService::ENTITY_ALIAS].'state', 'state')
            ->andWhere('state.id = :state')
                ->setParameter('state', $state)
        ;

        return $qb;
    }
}
