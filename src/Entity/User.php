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

    // TODO: REMOVE AFTER REFACTORING
    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isCredentialsExpired = false;

    // TODO: REMOVE AFTER REFACTORING
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $allowsEmail = null;

    // TODO: REMOVE AFTER REFACTORING
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $allowsEmailReminder = null;

    // TODO: REMOVE AFTER REFACTORING
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $toggleAllocSidebar = null;

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

    // TODO: REMOVE AFTER REFACTORING
    public function getHospital(): ?Hospital
    {
        return $this->hospitals->first();
    }

    // TODO: REMOVE AFTER REFACTORING
    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    // TODO: REMOVE AFTER REFACTORING
    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    // TODO: REMOVE AFTER REFACTORING
    public function getIsCredentialsExpired(): ?bool
    {
        return $this->isCredentialsExpired;
    }

    // TODO: REMOVE AFTER REFACTORING
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

    // TODO: REMOVE AFTER REFACTORING
    public function getIsParticipant(): ?bool
    {
        return $this->isParticipant;
    }

    // TODO: REMOVE AFTER REFACTORING
    public function setIsParticipant(?bool $isParticipant): self
    {
        $this->isParticipant = $isParticipant;

        return $this;
    }

    // TODO: REMOVE AFTER REFACTORING
    public function canImport(): bool
    {
        if ($this->hospitals->isEmpty()) {
            return false;
        }

        return true;
    }

    // TODO: REMOVE AFTER REFACTORING
    public function getAllowsEmail(): ?bool
    {
        return $this->allowsEmail;
    }

    // TODO: REMOVE AFTER REFACTORING
    public function setAllowsEmail(?bool $allowsEmail): self
    {
        $this->allowsEmail = $allowsEmail;

        return $this;
    }

    // TODO: REMOVE AFTER REFACTORING
    public function getAllowsEmailReminder(): ?bool
    {
        return $this->allowsEmailReminder;
    }

    // TODO: REMOVE AFTER REFACTORING
    public function setAllowsEmailReminder(?bool $allowsEmailReminder): self
    {
        $this->allowsEmailReminder = $allowsEmailReminder;

        return $this;
    }

    // TODO: REMOVE AFTER REFACTORING
    public function getToggleAllocSidebar(): ?bool
    {
        return $this->toggleAllocSidebar;
    }

    // TODO: REMOVE AFTER REFACTORING
    public function setToggleAllocSidebar(?bool $toggleAllocSidebar): self
    {
        $this->toggleAllocSidebar = $toggleAllocSidebar;

        return $this;
    }

    // TODO: REMOVE AFTER REFACTORING
    public function switchAllocSidebar(): self
    {
        if ($this->toggleAllocSidebar) {
            $this->toggleAllocSidebar = false;
        } else {
            $this->toggleAllocSidebar = true;
        }

        return $this;
    }
}
