<?php

namespace App\Domain\Command\Import;

use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\UserInterface;

class UpdateImportCommand
{
    public function __construct(
        private int $id,
        private string $name,
        private string $type,
        private UserInterface $user,
        private HospitalInterface $hospital,
        private bool $updateFile,
        private string $filePath,
        private string $fileMimeType,
        private string $fileExtension,
        private int $fileSize
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getHospital(): HospitalInterface
    {
        return $this->hospital;
    }

    public function getUpdateFile(): bool
    {
        return $this->updateFile;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getFileMimeType(): string
    {
        return $this->fileMimeType;
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }
}
