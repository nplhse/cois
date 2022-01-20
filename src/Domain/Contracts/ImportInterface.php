<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

interface ImportInterface
{
    public function setName(string $name): self;

    public function getName(): string;

    public function setType(string $type): self;

    public function getType(): string;

    public function setStatus(string $status): self;

    public function getStatus(): string;

    public function setUser(UserInterface $user): self;

    public function getUser(): UserInterface;

    public function setHospital(HospitalInterface $hospital): self;

    public function getHospital(): HospitalInterface;

    public function setFilePath(string $path): self;

    public function getFilePath(): string;

    public function setFileMimeType(string $mimeType): self;

    public function getFileMimeType(): string;

    public function setFileExtension(string $extension): self;

    public function getFileExtension(): string;

    public function setFileSize(int $size): self;

    public function getFileSize(): int;

    public function setRowCount(int $rowCount): self;

    public function getRowCount(): int;

    public function bumpRunCount(): self;

    public function getRunCount(): int;

    public function setRuntime(int $runtime): self;

    public function getRuntime(): int;
}
