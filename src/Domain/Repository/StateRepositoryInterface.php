<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Contracts\StateInterface;

interface StateRepositoryInterface extends EntityRepositoryInterface
{
    public function add(StateInterface $state): void;

    public function save(): void;

    public function delete(StateInterface $state): void;

    public function getById(int $id): StateInterface;
}
