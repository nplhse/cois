<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 *
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"user:read"}}
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"user:read"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Groups({"user:read"})
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
     *
     * @Groups({"user:read"})
     */
    private string $email;

    /**
     * @ORM\OneToOne(targetEntity=Hospital::class, mappedBy="owner", cascade={"persist", "remove"})
     */
    private Hospital $hospital;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCredentialsExpired;

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
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): string
    {
        return (string) $this->plainPassword;
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
        return (string) $this->getUsername();
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
}
