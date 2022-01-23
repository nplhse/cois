<?php

namespace App\Domain\Repository;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Entity\SupplyArea;

interface SupplyAreaRepositoryInterface
{
    public function add(SupplyAreaInterface $area): void;

    public function save(): void;

    public function delete(SupplyAreaInterface $area): void;

    public function getById(int $id): SupplyAreaInterface;
}
