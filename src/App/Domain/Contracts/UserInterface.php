<?php

declare(strict_types=1);

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

    public function setPassword(string $password): self;

    public function getPassword(): string;

    public function setPlainPassword(?string $plainPassword): self;

    public function getPlainPassword(): ?string;

    public function eraseCredentials(): void;

    public function addHospital(HospitalInterface $hospital): self;

    public function removeHospital(HospitalInterface $hospital): self;

    public function getHospitals(): \Doctrine\Common\Collections\Collection;

    public function isVerified(): bool;

    public function verify(): self;

    public function unverify(): self;

    public function isParticipant(): bool;

    public function enableParticipation(): self;

    public function disableParticipation(): self;

    public function hasCredentialsExpired(): bool;

    public function expireCredentials(): self;

    public function getFullname(): ?string;

    public function getName(): string;

    public function getLocation(): ?string;

    public function getBiography(): ?string;

    public function getWebsite(): ?string;
}
