<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contracts\TimestampableInterface;
use App\Domain\Contracts\UserInterface;
use App\Domain\Entity\Traits\IdentifierTrait;
use App\Domain\Entity\Traits\TimestampableTrait;

class User implements UserInterface, TimestampableInterface, \Stringable
{
    use IdentifierTrait;
    use TimestampableTrait;

    private string $username;

    private string $email;

    private array $roles;

    private bool $isVerified = false;

    private bool $isParticipant = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime('NOW');
        $this->roles = ['ROLE_USER'];
    }

    public function __toString(): string
    {
        return $this->username;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function addRole(string $role): self
    {
        $this->roles[] = $role;

        return $this;
    }

    /*
     * @throws \InvalidArgumentException
     */
    public function removeRole(string $role): self
    {
        if (in_array($role, $this->roles, true)) {
            $this->roles = array_merge(array_diff($this->roles, [$role]));

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('User has no Role %s', $role));
    }

    public function getRoles(): array
    {
        // Every user at least has ROLE_USER
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function verify(): UserInterface
    {
        $this->isVerified = true;

        return $this;
    }

    public function unverify(): UserInterface
    {
        $this->isVerified = false;

        return $this;
    }

    public function isParticipant(): bool
    {
        return $this->isParticipant;
    }

    public function enableParticipation(): UserInterface
    {
        $this->isParticipant = true;

        return $this;
    }

    public function disableParticipation(): UserInterface
    {
        $this->isParticipant = false;

        return $this;
    }
}
