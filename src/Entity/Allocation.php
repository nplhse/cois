<?php

namespace App\Entity;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\ImportInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Entity\Allocation as DomainAllocation;
use App\Repository\AllocationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AllocationRepository::class)]
class Allocation extends DomainAllocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    #[Assert\NotBlank(message: 'The Hospital must not be empty.')]
    #[ORM\ManyToOne(targetEntity: Hospital::class)]
    protected HospitalInterface $hospital;

    #[Assert\NotBlank(message: 'The Import must not be empty.')]
    #[ORM\ManyToOne(targetEntity: Import::class)]
    protected ImportInterface $import;

    #[Assert\NotBlank(message: 'The State must not be empty.')]
    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn]
    protected StateInterface $state;

    #[Assert\NotBlank(message: 'The DispatchArea must not be empty.')]
    #[ORM\ManyToOne(targetEntity: DispatchArea::class)]
    #[ORM\JoinColumn]
    protected DispatchAreaInterface $dispatchArea;

    #[ORM\ManyToOne(targetEntity: SupplyArea::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?SupplyAreaInterface $supplyArea = null;

    #[Assert\NotBlank(message: 'The createdAt must not be empty.')]
    #[ORM\Column(type: 'datetime')]
    protected \DateTimeInterface $createdAt;

    #[Assert\NotBlank(message: 'The creationDate must not be empty.')]
    #[ORM\Column(type: 'string', length: 10)]
    protected string $creationDate;

    #[Assert\NotBlank(message: 'The creationTime must not be empty.')]
    #[ORM\Column(type: 'string', length: 5)]
    protected string $creationTime;

    #[Assert\NotBlank(message: 'The creationDay must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $creationDay;

    #[Assert\NotBlank(message: 'The creationWeekday must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $creationWeekday;

    #[Assert\NotBlank(message: 'The creationMonth must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $creationMonth;

    #[Assert\NotBlank(message: 'The creationYear must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $creationYear;

    #[Assert\NotBlank(message: 'The creationHour must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $creationHour;

    #[Assert\NotBlank(message: 'The creationMinute must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $creationMinute;

    #[Assert\NotBlank(message: 'The arrivalAt must not be empty.')]
    #[ORM\Column(type: 'datetime')]
    protected \DateTimeInterface $arrivalAt;

    #[Assert\NotBlank(message: 'The arrivalDate must not be empty.')]
    #[ORM\Column(type: 'string', length: 10)]
    protected string $arrivalDate;

    #[Assert\NotBlank(message: 'The arrivalTime must not be empty.')]
    #[ORM\Column(type: 'string', length: 5)]
    protected string $arrivalTime;

    #[Assert\NotBlank(message: 'The arrivalDay must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $arrivalDay;

    #[Assert\NotBlank(message: 'The arrivalWeekday must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $arrivalWeekday;

    #[Assert\NotBlank(message: 'The arrivalMonth must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $arrivalMonth;

    #[Assert\NotBlank(message: 'The arrivalYear must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $arrivalYear;

    #[Assert\NotBlank(message: 'The arrivalHour must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $arrivalHour;

    #[Assert\NotBlank(message: 'The arrivalMinute must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $arrivalMinute;

    #[Assert\Choice([1, 2, 3])]
    #[Assert\NotBlank(message: 'The urgency must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $urgency;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    protected string $occasion;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    protected string $assignment;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: 'boolean')]
    protected bool $requiresResus;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: 'boolean')]
    protected bool $requiresCathlab;

    #[Assert\Choice(['M', 'W', 'D'])]
    #[ORM\Column(type: 'string', length: 1)]
    protected string $gender;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'integer')]
    protected int $age;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: 'boolean')]
    protected bool $isCPR;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: 'boolean')]
    protected bool $isVentilated;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: 'boolean')]
    protected bool $isShock;

    #[Assert\NotBlank(message: 'The isInfectious must not be empty.')]
    #[ORM\Column(type: 'string', length: 50)]
    protected string $isInfectious;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: 'boolean')]
    protected bool $isPregnant;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: 'boolean')]
    protected bool $isWorkAccident;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: 'boolean')]
    protected bool $isWithPhysician;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    protected string $modeOfTransport;

    #[Assert\NotBlank(message: 'The speciality must not be empty.')]
    #[ORM\Column(type: 'string', length: 50)]
    protected string $speciality;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    protected ?string $specialityDetail = null;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: 'boolean')]
    protected bool $specialityWasClosed;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    protected ?string $handoverPoint = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $comment = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    protected string $indication;

    #[Assert\NotBlank(message: 'The indicationCode must not be empty.')]
    #[ORM\Column(type: 'integer')]
    protected int $indicationCode;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    protected ?string $secondaryIndication = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $secondaryIndicationCode = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    protected ?string $secondaryDeployment = null;
}
