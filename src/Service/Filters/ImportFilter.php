<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Entity\User;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class ImportFilter implements FilterInterface
{
    use FilterTrait;

    public const Param = 'import';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getValue(Request $request): mixed
    {
        $import = (int) $request->query->get('import');

        if (empty($import)) {
            $value = null;
        }

        if ($import > 0) {
            $value = $import;
        } else {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $import = $this->cacheValue ?? $this->getValue($request);

        if (!isset($import)) {
            return $qb;
        }

        // If User is not an Admin deny access
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        return $qb->orWhere($arguments[FilterService::ENTITY_ALIAS].'import = :import')
            ->setParameter('import', $import)
            ;
    }
}
