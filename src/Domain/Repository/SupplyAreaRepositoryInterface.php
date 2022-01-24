<?php

namespace App\Domain\Repository;

use App\Domain\Contracts\SupplyAreaInterface;

interface SupplyAreaRepositoryInterface
{
    public function add(SupplyAreaInterface $area): void;

    public function save(): void;

    public function delete(SupplyAreaInterface $area): void;

    public function getById(int $id): SupplyAreaInterface;
}
