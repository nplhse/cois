<?php

namespace App\Domain\Command\Hospital;

class DeleteHospitalCommand
{
    public function __construct(
        private int $id
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
