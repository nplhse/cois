<?php

namespace App\Entity;

use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\UserInterface;
use App\Domain\Entity\Import as DomainImport;
use App\Repository\ImportRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass=ImportRepository::class)
 */
class Import extends DomainImport
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    protected UserInterface $user;

    /**
     * @ORM\ManyToOne(targetEntity=Hospital::class, inversedBy="imports")
     */
    protected HospitalInterface $hospital;

    /**
     * @ORM\Column(type="datetime")
     */
    protected \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTimeInterface $updatedAt = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?File $file = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $filePath;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $fileMimeType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $fileExtension;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $fileSize;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $rowCount = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $runCount = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $runtime = 0;

    /**
     * TODO: REMOVE after refactoring.
     *
     * @ORM\Column(type="integer")
     */
    private int $size;

    /**
     * TODO: REMOVE after refactoring.
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $path;

    /**
     * TODO: REMOVE after refactoring.
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $extension;

    /**
     * TODO: REMOVE after refactoring.
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $mimeType;

    /**
     * TODO: REMOVE after refactoring.
     *
     * @ORM\Column(type="boolean")
     */
    private bool $isFixture;

    /**
     * TODO: REMOVE after refactoring.
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $caption;

    /**
     * TODO: REMOVE after refactoring.
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $contents;

    /**
     * TODO: REMOVE after refactoring.
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $duration = null;

    /**
     * TODO: REMOVE after refactoring.
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private \DateTimeInterface $lastRun;

    /**
     * TODO: REMOVE after refactoring.
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $timesRun;

    /**
     * TODO: REMOVE after refactoring.
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $itemCount = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $lastError = null;

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): void
    {
        $this->file = $file;
    }

    public function __construct()
    {
        parent::__construct();

        $this->lastRun = new \DateTime('NOW');
        $this->timesRun = 0;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function isFixture(): bool
    {
        return $this->isFixture;
    }

    public function setIsFixture(bool $isFixture): void
    {
        $this->isFixture = $isFixture;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): void
    {
        $this->caption = $caption;
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function setContents(string $contents): void
    {
        $this->contents = $contents;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(?float $duration): void
    {
        $this->duration = $duration;
    }

    public function getLastRun(): \DateTimeInterface
    {
        return $this->lastRun;
    }

    public function setLastRun(\DateTimeInterface $lastRun): void
    {
        $this->lastRun = $lastRun;
    }

    public function getTimesRun(): ?int
    {
        return $this->timesRun;
    }

    public function setTimesRun(?int $timesRun): void
    {
        $this->timesRun = $timesRun;
    }

    public function getItemCount(): ?int
    {
        return $this->itemCount;
    }

    public function setItemCount(?int $itemCount): void
    {
        $this->itemCount = $itemCount;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function setLastError(?string $lastError): void
    {
        $this->lastError = $lastError;
    }
}
