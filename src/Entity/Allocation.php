<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\ImportInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Entity\Allocation as DomainAllocation;
use App\Repository\AllocationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AllocationRepository::class)]
#[Index(name: 'occasion_idx', columns: ['occasion'])]
#[Index(name: 'assignment_idx', columns: ['assignment'])]
#[Index(name: 'is_infectious_idx', columns: ['is_infectious'])]
#[Index(name: 'indication_idx', columns: ['indication'])]
#[Index(name: 'secondary_indication_idx', columns: ['secondary_indication'])]
#[Index(name: 'secondary_deployment_idx', columns: ['secondary_deployment'])]
#[Index(name: 'speciality_idx', columns: ['speciality'])]
#[Index(name: 'speciality_detail_idx', columns: ['speciality_detail'])]
class Allocation extends DomainAllocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
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
    #[ORM\JoinColumn]
    protected ?SupplyAreaInterface $supplyArea = null;

    #[Assert\NotBlank(message: 'The createdAt must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE)]
    protected \DateTimeInterface $createdAt;

    #[Assert\NotBlank(message: 'The creationDate must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 10)]
    protected string $creationDate;

    #[Assert\NotBlank(message: 'The creationTime must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 5)]
    protected string $creationTime;

    #[Assert\NotBlank(message: 'The creationDay must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $creationDay;

    #[Assert\NotBlank(message: 'The creationWeekday must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $creationWeekday;

    #[Assert\NotBlank(message: 'The creationMonth must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $creationMonth;

    #[Assert\NotBlank(message: 'The creationYear must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $creationYear;

    #[Assert\NotBlank(message: 'The creationHour must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $creationHour;

    #[Assert\NotBlank(message: 'The creationMinute must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $creationMinute;

    #[Assert\NotBlank(message: 'The arrivalAt must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE)]
    protected \DateTimeInterface $arrivalAt;

    #[Assert\NotBlank(message: 'The arrivalDate must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 10)]
    protected string $arrivalDate;

    #[Assert\NotBlank(message: 'The arrivalTime must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 5)]
    protected string $arrivalTime;

    #[Assert\NotBlank(message: 'The arrivalDay must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $arrivalDay;

    #[Assert\NotBlank(message: 'The arrivalWeekday must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $arrivalWeekday;

    #[Assert\NotBlank(message: 'The arrivalMonth must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $arrivalMonth;

    #[Assert\NotBlank(message: 'The arrivalYear must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $arrivalYear;

    #[Assert\NotBlank(message: 'The arrivalHour must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $arrivalHour;

    #[Assert\NotBlank(message: 'The arrivalMinute must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $arrivalMinute;

    #[Assert\Choice([1, 2, 3])]
    #[Assert\NotBlank(message: 'The urgency must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $urgency;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 50, nullable: true)]
    protected string $occasion;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 50, nullable: true)]
    protected string $assignment;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN)]
    protected bool $requiresResus;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN)]
    protected bool $requiresCathlab;

    #[Assert\Choice(['M', 'W', 'D'])]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 1)]
    protected string $gender;

    #[Assert\NotBlank]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $age;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN)]
    protected bool $isCPR;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN)]
    protected bool $isVentilated;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN)]
    protected bool $isShock;

    #[Assert\NotBlank(message: 'The isInfectious must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 50)]
    protected string $isInfectious;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN)]
    protected bool $isPregnant;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN)]
    protected bool $isWorkAccident;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN)]
    protected bool $isWithPhysician;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 10, nullable: true)]
    protected string $modeOfTransport;

    #[Assert\NotBlank(message: 'The speciality must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 50)]
    protected string $speciality;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 50, nullable: true)]
    protected ?string $specialityDetail = null;

    #[Assert\Type('boolean')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN)]
    protected bool $specialityWasClosed;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 200, nullable: true)]
    protected ?string $handoverPoint = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255, nullable: true)]
    protected ?string $comment = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 50, nullable: true)]
    protected string $indication;

    #[Assert\NotBlank(message: 'The indicationCode must not be empty.')]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $indicationCode;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 50, nullable: true)]
    protected ?string $secondaryIndication = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER, nullable: true)]
    protected ?int $secondaryIndicationCode = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 50, nullable: true)]
    protected ?string $secondaryDeployment = null;
}
