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
    protected HospitalInterface $hospital;

    /**
     * @ORM\ManyToOne(targetEntity=Import::class)
     */
    protected ImportInterface $import;

    /**
     * @ORM\ManyToOne(targetEntity=State::class)
     * @ORM\JoinColumn(nullable=true)
     */
    protected ?StateInterface $state = null;

    /**
     * @ORM\ManyToOne(targetEntity=DispatchArea::class)
     * @ORM\JoinColumn(nullable=true)
     */
    protected ?DispatchAreaInterface $dispatchArea = null;

    /**
     * @ORM\ManyToOne(targetEntity=SupplyArea::class)
     * @ORM\JoinColumn(nullable=true)
     */
    protected ?SupplyAreaInterface $supplyArea = null;

    /**
     * @ORM\Column(type="datetime")
     */
    protected \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected string $creationDate;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected string $creationTime;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $creationDay;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $creationWeekday;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $creationMonth;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $creationYear;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $creationHour;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $creationMinute;

    /**
     * @ORM\Column(type="datetime")
     */
    protected \DateTimeInterface $arrivalAt;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected string $arrivalDate;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected string $arrivalTime;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $arrivalDay;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $arrivalWeekday;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $arrivalMonth;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $arrivalYear;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $arrivalHour;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $arrivalMinute;

    /**
     * @ORM\Column(type="integer")
     */
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
    protected bool $requiresResus;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $requiresCathlab;

    /**
     * @ORM\Column(type="string", length=1)
     */
    protected string $gender;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $age;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $isCPR;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $isVentilated;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $isShock;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected string $isInfectious;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $isPregnant;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $isWorkAccident;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $isWithPhysician;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected string $modeOfTransport;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected string $speciality;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected string $specialityDetail;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $specialityWasClosed;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected string $handoverPoint;

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
    protected int $indicationCode;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected ?string $secondaryIndication = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected ?int $secondaryIndicationCode = null;
}
