<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\IdentifierInterface;
use App\Domain\Contracts\ImportInterface;
use App\Domain\Contracts\TimestampableInterface;
use App\Domain\Contracts\UserInterface;
use App\Domain\Entity\Traits\IdentifierTrait;
use App\Domain\Entity\Traits\TimestampableTrait;

class Import implements ImportInterface, IdentifierInterface, TimestampableInterface, \Stringable
{
    use IdentifierTrait;
    use TimestampableTrait;

    public const TYPE_ALLOCATION = 'allocation';

    public const STATUS_PENDING = 'pending';

    public const STATUS_FAILURE = 'failure';

    public const STATUS_INCOMPLETE = 'incomplete';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_EMPTY = 'empty';

    public const MIME_CSV = 'text/csv';

    public const MIME_PLAIN = 'text/plain';

    public const EXT_CSV = 'csv';

    protected string $name;

    private array $types = [self::TYPE_ALLOCATION];

    protected string $type;

    private array $statuses = [self::STATUS_PENDING, self::STATUS_FAILURE, self::STATUS_INCOMPLETE, self::STATUS_SUCCESS, self::STATUS_EMPTY];

    protected string $status;

    protected UserInterface $user;

    protected HospitalInterface $hospital;

    protected string $filePath;

    protected array $mimeTypes = [self::MIME_PLAIN, self::MIME_CSV];

    protected string $fileMimeType;

    protected array $extensions = [self::EXT_CSV];

    protected string $fileExtension;

    protected int $fileSize;

    protected int $rowCount = 0;

    protected int $runCount = 0;

    protected int $runtime = 0;

    protected ?int $skippedRows = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime('NOW');
    }

    public function __toString()
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setType(string $type): self
    {
        if (in_array($type, $this->types, true)) {
            $this->type = $type;

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('Type %s is not a valid option', $type));
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setStatus(string $status): self
    {
        if (in_array($status, $this->statuses, true)) {
            $this->status = $status;

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('Status %s is not a valid option', $status));
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setHospital(HospitalInterface $hospital): self
    {
        $this->hospital = $hospital;

        return $this;
    }

    public function getHospital(): HospitalInterface
    {
        return $this->hospital;
    }

    public function setFilePath(string $path): self
    {
        $this->filePath = $path;

        return $this;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFileMimeType(string $mimeType): self
    {
        if (in_array($mimeType, $this->mimeTypes, true)) {
            $this->fileMimeType = $mimeType;

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('MIME-Type %s is not supported', $mimeType));
    }

    public function getFileMimeType(): string
    {
        return $this->fileMimeType;
    }

    public function setFileExtension(string $extension): self
    {
        if (in_array($extension, $this->extensions, true)) {
            $this->fileExtension = $extension;

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('File extension %s is not supported', $extension));
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    public function setFileSize(int $size): self
    {
        if ($size >= 0) {
            $this->fileSize = $size;

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('File size must be positive integer, not %d', $size));
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    public function setRowCount(int $rowCount): self
    {
        if ($rowCount >= 0) {
            $this->rowCount = $rowCount;

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('Row count must be positive integer, not %d', $rowCount));
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function bumpRunCount(): self
    {
        ++$this->runCount;

        return $this;
    }

    public function getRunCount(): int
    {
        return $this->runCount;
    }

    public function setRuntime(int $runtime): self
    {
        if ($runtime >= 0) {
            $this->runtime = $runtime;

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('Runtime must be positive integer, not %d', $runtime));
    }

    public function getRuntime(): int
    {
        return $this->runtime;
    }

    public function getSkippedRows(): ?int
    {
        return $this->skippedRows ?? 0;
    }

    public function addSkippedRow(): void
    {
        ++$this->skippedRows;
    }
}
