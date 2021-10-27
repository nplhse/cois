<?php

namespace App\DataTransferObjects;

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
        dump($this->items);

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
