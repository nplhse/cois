<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Contracts\StateInterface;

interface StateRepositoryInterface extends EntityRepositoryInterface
{
    public function add(StateInterface $state): void;

    public function save(): void;

    public function delete(StateInterface $state): void;

    public function getById(int $id): StateInterface;
}
