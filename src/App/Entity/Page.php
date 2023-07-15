<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Contracts\UserInterface;
use App\Domain\Entity\Traits\BlamableTrait;
use App\Domain\Entity\Traits\TimestampableTrait;
use App\Domain\Enum\PageStatus;
use App\Domain\Enum\PageType;
use App\Domain\Enum\PageVisbility;
use App\Repository\PageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Page
{
    use BlamableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 50)]
    private string $title;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 50)]
    private string $slug;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::TEXT)]
    private string $content;

    #[ORM\Column(enumType: PageType::class)]
    private PageType $type;

    #[ORM\Column(enumType: PageStatus::class)]
    private PageStatus $status;

    #[ORM\Column(enumType: PageVisbility::class, nullable: true)]
    private ?PageVisbility $visibility = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE)]
    protected \DateTimeInterface $createdAt;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected UserInterface $createdBy;

    #[ORM\ManyToOne(targetEntity: User::class)]
    protected ?UserInterface $updatedBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getType(): PageType
    {
        return $this->type;
    }

    public function setType(PageType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): PageStatus
    {
        return $this->status;
    }

    public function setStatus(PageStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getVisibility(): ?PageVisbility
    {
        return $this->visibility;
    }

    public function setVisibility(?PageVisbility $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }
}
