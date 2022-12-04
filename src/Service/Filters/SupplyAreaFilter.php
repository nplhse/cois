<?php

declare(strict_types=1);

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Repository\SupplyAreaRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class SupplyAreaFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'supplyArea';

    public function __construct(
        private SupplyAreaRepository $supplyAreaRepository,
        private TagAwareCacheInterface $cache
    ) {
    }

    public function getValue(Request $request): mixed
    {
        $area = $request->query->get('supplyArea');

        if (empty($area)) {
            $value = null;
        } else {
            $value = (int) urldecode($area);
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): array
    {
        return $this->cache->get('supply_area_filter', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $item->tag(['filter', 'supply_area_filter']);

            $qb = $this->supplyAreaRepository->createQueryBuilder('s');
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
        $area = $this->cacheValue ?? $this->getValue($request);

        if (!isset($area)) {
            return $qb;
        }

        $qb->leftJoin($arguments[FilterService::ENTITY_ALIAS].'supplyArea', 'supplyArea')
            ->andWhere('supplyArea.id = :supplyArea')
                ->setParameter('supplyArea', $area)
        ;

        return $qb;
    }
}
