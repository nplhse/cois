<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Contracts\SupplyAreaInterface;

interface SupplyAreaRepositoryInterface extends EntityRepositoryInterface
{
    public function add(SupplyAreaInterface $area): void;

    public function save(): void;

    public function delete(SupplyAreaInterface $area): void;

    public function getById(int $id): SupplyAreaInterface;
}
