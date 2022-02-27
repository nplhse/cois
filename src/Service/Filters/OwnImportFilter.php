<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class OwnImportFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'ownImports';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getValue(Request $request): mixed
    {
        $ownImports = $request->query->get('ownImports');

        if (empty($ownImports)) {
            $value = null;
        }

        if (1 === (int) $ownImports) {
            $value = 1;
        } else {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): mixed
    {
        return ['1' => 'Own Imports'];
    }

    public function getType(): string
    {
        return 'boolean';
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $ownImports = $this->cacheValue ?? $this->getValue($request);

        if (!isset($ownImports)) {
            return $qb;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'user = :owner')
            ->setParameter('owner', $this->security->getUser())
        ;
    }
}
