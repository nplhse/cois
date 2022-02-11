<?php

namespace App\Entity;

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
    protected User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Hospital::class, inversedBy="imports")
     */
    protected Hospital $hospital;

    /**
     * @ORM\Column(type="datetime")
     */
    protected \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTimeInterface $updatedAt;

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

    public function __construct()
    {
        parent::__construct();

        // TODO: Remove after Refactoring
        $this->lastRun = new \DateTime('NOW');
        $this->timesRun = 0;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getIsFixture(): ?bool
    {
        return $this->isFixture;
    }

    public function setIsFixture(bool $isFixture): self
    {
        $this->isFixture = $isFixture;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getContents(): ?string
    {
        return $this->contents;
    }

    public function setContents(string $contents): self
    {
        $this->contents = $contents;

        return $this;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(?float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getLastRun(): ?\DateTimeInterface
    {
        return $this->lastRun;
    }

    public function setLastRun(?\DateTimeInterface $lastRun): self
    {
        $this->lastRun = $lastRun;

        return $this;
    }

    public function getTimesRun(): ?int
    {
        return $this->timesRun;
    }

    public function setTimesRun(?int $timesRun): self
    {
        $this->timesRun = $timesRun;

        return $this;
    }

    public function getItemCount(): ?int
    {
        return $this->itemCount;
    }

    public function setItemCount(?int $itemCount): self
    {
        $this->itemCount = $itemCount;

        return $this;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function setLastError(?string $lastError): self
    {
        $this->lastError = $lastError;

        return $this;
    }
}
