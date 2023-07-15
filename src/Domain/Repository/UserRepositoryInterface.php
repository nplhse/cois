<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Contracts\UserInterface;

interface UserRepositoryInterface extends EntityRepositoryInterface
{
    public function add(UserInterface $user): void;

    public function save(): void;

    public function delete(UserInterface $user): void;

    public function findOneById(int $id): ?UserInterface;

    public function findOneByUsername(string $username): ?UserInterface;

    public function findOneByEmail(string $email): ?UserInterface;

    public function findAdmins(): array;

    public function findHospitalOwners(): array;
}
