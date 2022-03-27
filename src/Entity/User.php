<?php

namespace App\Entity;

use App\Domain\Entity\User as DomainUser;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
#[UniqueEntity(fields: ['username'], message: 'Please choose a different username')]
#[UniqueEntity(fields: ['email'], message: 'Please choose a different email address')]
class User extends DomainUser implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    protected string $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected string $email;

    /**
     * @ORM\Column(type="json")
     *
     * @var array<string>
     */
    protected array $roles = [];

    /**
     * @ORM\Column(type="string")
     */
    protected string $password;

    protected ?string $plainPassword = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTimeInterface $updatedAt = null;

    /**
     * @ORM\OneToMany(targetEntity=Hospital::class, mappedBy="owner")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected \Doctrine\Common\Collections\Collection $hospitals;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $isVerified = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $isParticipant = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $hasCredentialsExpired = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected ?bool $acceptedTerms;

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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }
}
