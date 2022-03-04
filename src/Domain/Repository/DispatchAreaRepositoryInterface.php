<?php

namespace App\Domain\Repository;

use App\Domain\Contracts\DispatchAreaInterface;

interface DispatchAreaRepositoryInterface extends EntityRepositoryInterface
{
    public function add(DispatchAreaInterface $area): void;

    public function save(): void;

    public function delete(DispatchAreaInterface $area): void;

    public function getById(int $id): DispatchAreaInterface;
}
