<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Contracts\AllocationInterface;

interface AllocationRepositoryInterface extends EntityRepositoryInterface
{
    public function add(AllocationInterface $allocation): void;

    public function save(): void;

    public function delete(AllocationInterface $allocation): void;
}
