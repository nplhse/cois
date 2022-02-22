<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ModeOfTransportFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'modeOfTransport';

    public function getValue(Request $request): mixed
    {
        $modeOfTransport = $request->query->get('modeOfTransport');

        if (empty($modeOfTransport)) {
            $value = null;
        } else {
            $value = urldecode($modeOfTransport);
        }

        return $this->setCacheValue($value);
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
        $modeOfTransport = $this->cacheValue ?? $this->getValue($request);

        if (!isset($modeOfTransport)) {
            return $qb;
        }

        $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'modeOfTransport = :modeOfTransport')
                ->setParameter('modeOfTransport', $modeOfTransport)
            ;

        return $qb;
    }
}
