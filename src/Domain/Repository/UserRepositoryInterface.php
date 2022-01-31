<?php

namespace App\Domain\Repository;

use App\Domain\Contracts\UserInterface;

interface UserRepositoryInterface
{
    public function add(UserInterface $state): void;

    public function save(): void;

    public function delete(UserInterface $state): void;
}
