<?php

namespace App\Domain\Contracts;

interface UserInterface
{
    public function getId(): int;

    public function getUserIdentifier(): string;

    public function setUsername(string $username): self;

    public function getUsername(): string;

    public function setEmail(string $email): self;

    public function getEmail(): string;

    public function addRole(string $role): self;

    public function removeRole(string $role): self;

    public function getRoles(): array;

    public function isVerified(): bool;

    public function verify(): self;

    public function unverify(): self;

    public function isParticipant(): bool;

    public function enableParticipation(): self;

    public function disableParticipation(): self;
}
