<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $username;

    /**
     * @ORM\Column(type="json")
     *
     * @var array<string>
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     */
    private string $password;

    private ?string $plainPassword = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $email;

    /**
     * @ORM\OneToOne(targetEntity=Hospital::class, mappedBy="owner", cascade={"persist", "remove"})
     */
    private ?Hospital $hospital = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isVerified = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isCredentialsExpired = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isParticipant = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $allowsEmail;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $allowsEmailReminder;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        $this->password = '';

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(Hospital $hospital): self
    {
        // set the owning side of the relation if necessary
        if ($hospital->getOwner() !== $this) {
            $hospital->setOwner($this);
        }

        $this->hospital = $hospital;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getIsCredentialsExpired(): ?bool
    {
        return $this->isCredentialsExpired;
    }

    public function setIsCredentialsExpired(bool $isCredentialsExpired): self
    {
        $this->isCredentialsExpired = $isCredentialsExpired;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function __toString(): string
    {
        return $this->getUsername();
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function getIsParticipant(): ?bool
    {
        return $this->isParticipant;
    }

    public function setIsParticipant(?bool $isParticipant): self
    {
        $this->isParticipant = $isParticipant;

        return $this;
    }

    public function canImport(): bool
    {
        if ($this->hospital) {
            return true;
        }

        return false;
    }

    public function getAllowsEmail(): ?bool
    {
        return $this->allowsEmail;
    }

    public function setAllowsEmail(?bool $allowsEmail): self
    {
        $this->allowsEmail = $allowsEmail;

        return $this;
    }

    public function getAllowsEmailReminder(): ?bool
    {
        return $this->allowsEmailReminder;
    }

    public function setAllowsEmailReminder(?bool $allowsEmailReminder): self
    {
        $this->allowsEmailReminder = $allowsEmailReminder;

        return $this;
    }
}
