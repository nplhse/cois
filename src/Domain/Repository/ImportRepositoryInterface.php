<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Contracts\ImportInterface;

interface ImportRepositoryInterface extends EntityRepositoryInterface
{
    public function add(ImportInterface $import): void;

    public function save(): void;

    public function delete(ImportInterface $import): void;
}
