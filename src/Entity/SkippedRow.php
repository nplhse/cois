<?php

namespace App\Entity;

use App\Repository\SkippedRowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkippedRowRepository::class)]
class SkippedRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Import::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Import $import;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::TEXT)]
    private string $errors;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::JSON)]
    private array $data = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImport(): ?Import
    {
        return $this->import;
    }

    public function setImport(?Import $import): self
    {
        $this->import = $import;

        return $this;
    }

    public function getErrors(): string
    {
        return $this->errors;
    }

    public function setErrors(string $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
