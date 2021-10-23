<?php

namespace App\DataTransferObjects;

interface ResultCollectionInterface
{
    public function getSingleResult();

    public function hydrateSingleResultAs(string $className);

    public function hydrateResultsAs(string $className);
}
