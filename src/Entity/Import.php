<?php

namespace App\Entity;

use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\UserInterface;
use App\Domain\Entity\Import as DomainImport;
use App\Repository\ImportRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: ImportRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Import extends DomainImport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $name;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $type;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $status;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected UserInterface $user;

    #[ORM\ManyToOne(targetEntity: Hospital::class, inversedBy: 'imports')]
    protected HospitalInterface $hospital;

    #[ORM\Column(type: 'datetime')]
    protected \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $updatedAt;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $filePath;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $fileMimeType;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $fileExtension;

    #[ORM\Column(type: 'integer')]
    protected int $fileSize;

    #[ORM\Column(type: 'integer')]
    protected int $rowCount = 0;

    #[ORM\Column(type: 'integer')]
    protected int $runCount = 0;

    #[ORM\Column(type: 'integer')]
    protected int $runtime = 0;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lastError = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $skippedRows = 0;

    private ?File $file = null;

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): void
    {
        $this->file = $file;
    }
}
