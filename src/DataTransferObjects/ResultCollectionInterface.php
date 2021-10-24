<?php

namespace App\DataTransferObjects;

interface ResultCollectionInterface
{
    public function getSingleResult(): array;

    public function hydrateSingleResultAs(string $className): object;

    public function hydrateResultsAs(string $className): object;
}
