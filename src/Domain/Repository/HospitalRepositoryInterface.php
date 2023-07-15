<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Contracts\HospitalInterface;

interface HospitalRepositoryInterface extends EntityRepositoryInterface
{
    public function add(HospitalInterface $hospital): void;

    public function save(): void;

    public function delete(HospitalInterface $hospital): void;

    public function findOneById(int $id): ?HospitalInterface;

    public function findOneByTriplet(string $name, string $location, int $beds): ?HospitalInterface;
}
