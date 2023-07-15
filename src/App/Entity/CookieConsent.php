<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CookieConsentRepository;
use Doctrine\ORM\Mapping as ORM;
use Domain\Entity\Traits\TimestampableTrait;

#[ORM\Entity(repositoryClass: CookieConsentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class CookieConsent
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 32)]
    private string $lookupKey;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 45)]
    private string $ipAddress;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::ARRAY)]
    private array $categories = [];

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE)]
    protected \DateTimeInterface $createdAt;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLookupKey(): ?string
    {
        return $this->lookupKey;
    }

    public function setLookupKey(string $lookupKey): self
    {
        $this->lookupKey = $lookupKey;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getCategories(): ?array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }
}
