<?php

namespace App\Domain\Repository;

use App\Domain\Contracts\ImportInterface;

interface ImportRepositoryInterface extends EntityRepositoryInterface
{
    public function add(ImportInterface $import): void;

    public function save(): void;

    public function delete(ImportInterface $import): void;
}
