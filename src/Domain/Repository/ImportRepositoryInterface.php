<?php

namespace App\Domain\Repository;

use App\Domain\Contracts\ImportInterface;
use App\Domain\Contracts\UserInterface;

interface ImportRepositoryInterface
{
    public function add(ImportInterface $import): void;

    public function save(): void;

    public function delete(ImportInterface $import): void;
}
