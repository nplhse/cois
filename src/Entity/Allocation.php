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

/**
 * @ORM\Entity(repositoryClass=AllocationRepository::class)
 */
class Allocation extends DomainAllocation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Hospital::class)
     */
    #[Assert\NotBlank(message: 'The Hospital must not be empty.')]
    protected HospitalInterface $hospital;

    /**
     * @ORM\ManyToOne(targetEntity=Import::class)
     */
    #[Assert\NotBlank(message: 'The Import must not be empty.')]
    protected ImportInterface $import;

    /**
     * @ORM\ManyToOne(targetEntity=State::class)
     * @ORM\JoinColumn()
     */
    #[Assert\NotBlank(message: 'The State must not be empty.')]
    protected StateInterface $state;

    /**
     * @ORM\ManyToOne(targetEntity=DispatchArea::class)
     * @ORM\JoinColumn()
     */
    #[Assert\NotBlank(message: 'The DispatchArea must not be empty.')]
    protected DispatchAreaInterface $dispatchArea;

    /**
     * @ORM\ManyToOne(targetEntity=SupplyArea::class)
     * @ORM\JoinColumn(nullable=true)
     */
    protected ?SupplyAreaInterface $supplyArea = null;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Assert\NotBlank(message: 'The createdAt must not be empty.')]
    protected \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="string", length=10)
     */
    #[Assert\NotBlank(message: 'The creationDate must not be empty.')]
    protected string $creationDate;

    /**
     * @ORM\Column(type="string", length=5)
     */
    #[Assert\NotBlank(message: 'The creationTime must not be empty.')]
    protected string $creationTime;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The creationDay must not be empty.')]
    protected int $creationDay;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The creationWeekday must not be empty.')]
    protected int $creationWeekday;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The creationMonth must not be empty.')]
    protected int $creationMonth;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The creationYear must not be empty.')]
    protected int $creationYear;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The creationHour must not be empty.')]
    protected int $creationHour;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The creationMinute must not be empty.')]
    protected int $creationMinute;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Assert\NotBlank(message: 'The arrivalAt must not be empty.')]
    protected \DateTimeInterface $arrivalAt;

    /**
     * @ORM\Column(type="string", length=10)
     */
    #[Assert\NotBlank(message: 'The arrivalDate must not be empty.')]
    protected string $arrivalDate;

    /**
     * @ORM\Column(type="string", length=5)
     */
    #[Assert\NotBlank(message: 'The arrivalTime must not be empty.')]
    protected string $arrivalTime;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The arrivalDay must not be empty.')]
    protected int $arrivalDay;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The arrivalWeekday must not be empty.')]
    protected int $arrivalWeekday;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The arrivalMonth must not be empty.')]
    protected int $arrivalMonth;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The arrivalYear must not be empty.')]
    protected int $arrivalYear;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The arrivalHour must not be empty.')]
    protected int $arrivalHour;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The arrivalMinute must not be empty.')]
    protected int $arrivalMinute;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\Choice([1, 2, 3])]
    #[Assert\NotBlank(message: 'The urgency must not be empty.')]
    protected int $urgency;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected string $occasion;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected string $assignment;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Assert\Type('boolean')]
    protected bool $requiresResus;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Assert\Type('boolean')]
    protected bool $requiresCathlab;

    /**
     * @ORM\Column(type="string", length=1)
     */
    #[Assert\Choice(['M', 'W', 'D'])]
    protected string $gender;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank]
    protected int $age;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Assert\Type('boolean')]
    protected bool $isCPR;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Assert\Type('boolean')]
    protected bool $isVentilated;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Assert\Type('boolean')]
    protected bool $isShock;

    /**
     * @ORM\Column(type="string", length=50)
     */
    #[Assert\NotBlank(message: 'The isInfectious must not be empty.')]
    protected string $isInfectious;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Assert\Type('boolean')]
    protected bool $isPregnant;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Assert\Type('boolean')]
    protected bool $isWorkAccident;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Assert\Type('boolean')]
    protected bool $isWithPhysician;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected string $modeOfTransport;

    /**
     * @ORM\Column(type="string", length=50)
     */
    #[Assert\NotBlank(message: 'The speciality must not be empty.')]
    protected string $speciality;

    /**
     * @ORM\Column(type="string", length=50)
     */
    #[Assert\NotBlank(message: 'The specialityDetail must not be empty.')]
    protected string $specialityDetail;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Assert\Type('boolean')]
    protected bool $specialityWasClosed;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected ?string $handoverPoint = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $comment = null;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected string $indication;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: 'The indicationCode must not be empty.')]
    protected int $indicationCode;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected ?string $secondaryIndication = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected ?int $secondaryIndicationCode = null;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected ?string $secondaryDeployment = null;
}
