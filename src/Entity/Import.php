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
}
