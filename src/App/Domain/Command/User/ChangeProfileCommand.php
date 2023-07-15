<?php

declare(strict_types=1);

namespace App\Domain\Command\User;

class ChangeProfileCommand
{
    public function __construct(
        private int $id,
        private ?string $fullName,
        private ?string $biography,
        private ?string $location,
        private ?string $website,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }
}
