<?php

namespace App\Application\Contract;

interface ResultCollectionInterface
{
    public function getSingleResult(): array;

    public function hydrateSingleResultAs(string $className): object;

    public function hydrateResultsAs(string $className): object;

    public function getItems(): array;

    public function getIterable(): iterable;
}
