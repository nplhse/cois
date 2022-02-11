<?php

namespace App\Domain\Command\Import;

use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\UserInterface;

class CreateImportCommand
{
    private string $name;

    private string $type;

    private UserInterface $user;

    private HospitalInterface $hospital;

    private string $filePath;

    private string $fileMimeType;

    private string $fileExtension;

    private int $fileSize;

    public function __construct(string $name, string $type, UserInterface $user, HospitalInterface $hospital, string $filePath, string $fileMimeType, string $fileExtension, int $fileSize)
    {
        $this->name = $name;
        $this->type = $type;
        $this->user = $user;
        $this->hospital = $hospital;
        $this->filePath = $filePath;
        $this->fileMimeType = $fileMimeType;
        $this->fileExtension = $fileExtension;
        $this->fileSize = $fileSize;
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
