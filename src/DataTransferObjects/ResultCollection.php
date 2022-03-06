<?php

namespace App\DataTransferObjects;

use App\Application\Contract\ResultCollectionInterface;

final class ResultCollection implements ResultCollectionInterface
{
    private array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function getSingleResult(): array
    {
        return \reset($this->items);
    }

    public function hydrateSingleResultAs(string $className): object
    {
        $item = $this->getSingleResult();

        return $className::fromArray($item);
    }

    public function hydrateResultsAs(string $className): object
    {
        $hydratedItems = [];
        foreach ($this->items as $item) {
            $hydratedItems[] = $className::fromArray($item);
        }

        return new self($hydratedItems);
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
