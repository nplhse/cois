<?php

declare(strict_types=1);

namespace App\Service;

use App\Application\Contract\FilterInterface;
use App\Application\Exception\FilterMissingArgumentException;
use App\Application\Exception\FilterNotFoundException;
use App\DataTransferObjects\FilterDto;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class FilterService
{
    public const ENTITY_ALIAS = 'entity_alias';

    private Request $request;

    /** @var iterable|array<FilterInterface> */
    private iterable $filters;

    private array $availableFilters;

    private bool $strict = false;

    public function __construct(iterable $filters)
    {
        $this->filters = $filters instanceof \Traversable ? iterator_to_array($filters) : $filters;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function configureFilters(array $availableFilters, bool $strict = false): void
    {
        $this->availableFilters = $availableFilters;
        $this->strict = $strict;
    }

    public function getValue(string $filter): mixed
    {
        if (in_array($filter, $this->availableFilters, true)) {
            return $this->filters[$filter]->get($this->request);
        }

        if ($this->strict) {
            throw new FilterNotFoundException('Could not find filter: '.$filter);
        }

        return null;
    }

    public function processQuery(QueryBuilder $qb, array $arguments): mixed
    {
        if (!array_key_exists(self::ENTITY_ALIAS, $arguments)) {
            throw new FilterMissingArgumentException(sprintf('FilterService expected argument %s which is missing', strtoupper(self::ENTITY_ALIAS)));
        }

        foreach ($this->availableFilters as $filter) {
            $qb = $this->filters[$filter]->processQuery($qb, $arguments, $this->request);
        }

        return $qb;
    }

    public function getFilterDto(): FilterDto
    {
        $filterDto = new FilterDto();

        foreach ($this->availableFilters as $filter) {
            if ($this->filters[$filter]->getValue($this->request)) {
                if (method_exists($this->filters[$filter], 'getAltValues')) {
                    $altValues = $this->filters[$filter]->getAltValues();
                } else {
                    $altValues = [];
                }

                if (method_exists($this->filters[$filter], 'getType')) {
                    $type = $this->filters[$filter]->getType();
                } else {
                    $type = 'string';
                }

                $key = $this->filters[$filter]->getParam();
                $value = $this->filters[$filter]->getValue($this->request);

                $filterDto->addFilter($key, $value, $altValues, $type);
            }
        }

        return $filterDto;
    }
}
