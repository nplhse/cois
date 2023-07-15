<?php

declare(strict_types=1);

namespace App\Domain\Command\Import;

use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\UserInterface;

class CreateImportCommand
{
    public function __construct(
        private string $name,
        private string $type,
        private UserInterface $user,
        private HospitalInterface $hospital,
        private string $filePath,
        private string $fileMimeType,
        private string $fileExtension,
        private int $fileSize
    ) {
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
