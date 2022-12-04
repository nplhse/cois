<?php

declare(strict_types=1);

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Repository\DispatchAreaRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class DispatchAreaFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'dispatchArea';

    public function __construct(
        private DispatchAreaRepository $dispatchAreaRepository,
        private TagAwareCacheInterface $cache
    ) {
    }

    public function getValue(Request $request): mixed
    {
        $area = $request->query->get('dispatchArea');

        if (empty($area)) {
            $value = null;
        } else {
            $value = (int) urldecode($area);
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): array
    {
        return $this->cache->get('dispatch_area_filter', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $item->tag(['filter', 'dispatch_area_filter']);

            $qb = $this->dispatchAreaRepository->createQueryBuilder('d');
            $result = $qb->select('d.id, d.name')
                ->orderBy('d.id')
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
        $area = $this->cacheValue ?? $this->getValue($request);

        if (!isset($area)) {
            return $qb;
        }

        $qb->leftJoin($arguments[FilterService::ENTITY_ALIAS].'dispatchArea', 'dispatchArea')
            ->andWhere('dispatchArea.id = :dispatchArea')
                ->setParameter('dispatchArea', $area)
        ;

        return $qb;
    }
}
