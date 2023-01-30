<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\UserPasswordListener;
use App\Domain\Entity\User as DomainUser;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[UniqueEntity(fields: ['username'], message: 'Please choose a different username')]
#[UniqueEntity(fields: ['email'], message: 'Please choose a different email address')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Orm\EntityListeners([UserPasswordListener::class])]
class User extends DomainUser implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $id;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 180, unique: true)]
    protected string $username;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255, unique: true)]
    protected string $email;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::JSON)]
    protected array $roles = [];

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING)]
    protected string $password;

    protected ?string $plainPassword = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE, nullable: true)]
    protected \DateTimeInterface $createdAt;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $updatedAt;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\Hospital>|\App\Entity\Hospital[]
     */
    #[ORM\OneToMany(targetEntity: Hospital::class, mappedBy: 'owner')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    protected \Doctrine\Common\Collections\Collection $hospitals;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN)]
    protected bool $isVerified = false;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN)]
    protected bool $isParticipant = false;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN)]
    protected bool $hasCredentialsExpired = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fullName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $biography = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

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

    public function __construct()
    {
        parent::__construct();
        $this->hospitals = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->roles, true);
    }

    public function isMember(): bool
    {
        return in_array('ROLE_MEMBER', $this->roles, true);
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
