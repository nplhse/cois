<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Application\Contract\ResultCollectionInterface;

final class ResultCollection implements ResultCollectionInterface
{
    public function __construct(
        private array $items
    ) {
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

    public function getIterable(): iterable
    {
        return new \ArrayObject($this->items);
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
