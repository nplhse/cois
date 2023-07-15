<?php

declare(strict_types=1);

namespace Domain\Entity;

use Domain\Adapter\ArrayCollection;
use Domain\Contracts\HospitalInterface;
use Domain\Contracts\TimestampableInterface;
use Domain\Contracts\UserInterface;
use Domain\Entity\Traits\IdentifierTrait;
use Domain\Entity\Traits\TimestampableTrait;

class User implements UserInterface, TimestampableInterface, \Stringable
{
    use IdentifierTrait;
    use TimestampableTrait;

    protected string $username;

    protected string $email;

    protected array $roles;

    protected string $password;

    protected ?string $plainPassword = null;

    protected \Doctrine\Common\Collections\Collection $hospitals;

    protected bool $isVerified = false;

    protected bool $isParticipant = false;

    protected bool $hasCredentialsExpired = false;

    protected ?bool $acceptedTerms = false;

    protected ?string $fullName = null;

    protected ?string $location = null;

    protected ?string $biography = null;

    protected ?string $website = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime('NOW');
        $this->roles = ['ROLE_USER'];
        $this->hospitals = new ArrayCollection();
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

    /**
     * @return array<array-key, string>
     */
    public function getRoles(): array
    {
        // Every user at least has ROLE_USER
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setPassword(string $password): UserInterface
    {
        $this->password = $password;
        $this->hasCredentialsExpired = false;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPlainPassword(?string $plainPassword): UserInterface
    {
        if (null !== $plainPassword) {
            $this->plainPassword = $plainPassword;
            $this->password = '';

            return $this;
        }

        $this->plainPassword = null;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function addHospital(HospitalInterface $hospital): self
    {
        if (!$this->hospitals->contains($hospital)) {
            $this->hospitals[] = $hospital;

            $hospital->setOwner($this);
        }

        return $this;
    }

    public function removeHospital(HospitalInterface $hospital): self
    {
        $this->hospitals->removeElement($hospital);

        return $this;
    }

    public function getHospitals(): \Doctrine\Common\Collections\Collection
    {
        return $this->hospitals;
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

    public function hasCredentialsExpired(): bool
    {
        return $this->hasCredentialsExpired;
    }

    public function expireCredentials(): self
    {
        $this->hasCredentialsExpired = true;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getName(): string
    {
        if ($this->fullName) {
            return $this->fullName;
        }

        return $this->username;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }
}
