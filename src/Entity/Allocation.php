<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AllocationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AllocationRepository::class)
 *
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
 */
class Allocation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Hospital::class)
     */
    private Hospital $hospital;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $dispatchArea;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $supplyArea = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $creationDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $creationTime;

    /**
     * @ORM\Column(type="integer")
     */
    private int $creationDay;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $creationWeekday;

    /**
     * @ORM\Column(type="integer")
     */
    private int $creationMonth;

    /**
     * @ORM\Column(type="integer", length=255)
     */
    private int $creationYear;

    /**
     * @ORM\Column(type="integer")
     */
    private int $creationHour;

    /**
     * @ORM\Column(type="integer")
     */
    private int $creationMinute;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $arrivalAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $arrivalDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $arrivalTime;

    /**
     * @ORM\Column(type="integer")
     */
    private int $arrivalDay;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $arrivalWeekday;

    /**
     * @ORM\Column(type="integer")
     */
    private int $arrivalMonth;

    /**
     * @ORM\Column(type="integer")
     */
    private int $arrivalYear;

    /**
     * @ORM\Column(type="integer")
     */
    private int $arrivalHour;

    /**
     * @ORM\Column(type="integer")
     */
    private int $arrivalMinute;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $requiresResus;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $requiresCathlab;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $occasion = null;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private string $gender;

    /**
     * @ORM\Column(type="integer")
     */
    private int $age;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isCPR;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isVentilated;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isShock;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $isInfectious;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isPregnant;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isWithPhysician;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $assignment = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $modeOfTransport;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $comment = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $speciality;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $specialityDetail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $handoverPoint;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $specialityWasClosed;

    /**
     * @ORM\Column(type="integer")
     */
    private int $PZC;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $PZCText;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $SecondaryPZC;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $SecondaryPZCText;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;

        return $this;
    }

    public function getDispatchArea(): ?string
    {
        return $this->dispatchArea;
    }

    public function setDispatchArea(string $dispatchArea): self
    {
        $this->dispatchArea = $dispatchArea;

        return $this;
    }

    public function getSupplyArea(): ?string
    {
        return $this->supplyArea;
    }

    public function setSupplyArea(?string $supplyArea): self
    {
        $this->supplyArea = $supplyArea;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreationDate(): ?string
    {
        return $this->creationDate;
    }

    public function setCreationDate(string $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getCreationTime(): ?string
    {
        return $this->creationTime;
    }

    public function setCreationTime(string $creationTime): self
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    public function getCreationDay(): ?int
    {
        return $this->creationDay;
    }

    public function setCreationDay(int $creationDay): self
    {
        $this->creationDay = $creationDay;

        return $this;
    }

    public function getCreationWeekday(): ?string
    {
        return $this->creationWeekday;
    }

    public function setCreationWeekday(string $creationWeekday): self
    {
        $this->creationWeekday = $creationWeekday;

        return $this;
    }

    public function getCreationMonth(): ?int
    {
        return $this->creationMonth;
    }

    public function setCreationMonth(int $creationMonth): self
    {
        $this->creationMonth = $creationMonth;

        return $this;
    }

    public function getCreationYear(): ?int
    {
        return $this->creationYear;
    }

    public function setCreationYear(int $creationYear): self
    {
        $this->creationYear = $creationYear;

        return $this;
    }

    public function getCreationHour(): ?int
    {
        return $this->creationHour;
    }

    public function setCreationHour(int $creationHour): self
    {
        $this->creationHour = $creationHour;

        return $this;
    }

    public function getCreationMinute(): ?int
    {
        return $this->creationMinute;
    }

    public function setCreationMinute(int $creationMinute): self
    {
        $this->creationMinute = $creationMinute;

        return $this;
    }

    public function getArrivalAt(): ?\DateTimeInterface
    {
        return $this->arrivalAt;
    }

    public function setArrivalAt(\DateTimeInterface $arrivalAt): self
    {
        $this->arrivalAt = $arrivalAt;

        return $this;
    }

    public function getArrivalDate(): ?string
    {
        return $this->arrivalDate;
    }

    public function setArrivalDate(string $arrivalDate): self
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }

    public function getArrivalTime(): ?string
    {
        return $this->arrivalTime;
    }

    public function setArrivalTime(string $arrivalTime): self
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    public function getArrivalDay(): ?int
    {
        return $this->arrivalDay;
    }

    public function setArrivalDay(int $arrivalDay): self
    {
        $this->arrivalDay = $arrivalDay;

        return $this;
    }

    public function getArrivalWeekday(): ?string
    {
        return $this->arrivalWeekday;
    }

    public function setArrivalWeekday(string $arrivalWeekday): self
    {
        $this->arrivalWeekday = $arrivalWeekday;

        return $this;
    }

    public function getArrivalMonth(): ?int
    {
        return $this->arrivalMonth;
    }

    public function setArrivalMonth(int $arrivalMonth): self
    {
        $this->arrivalMonth = $arrivalMonth;

        return $this;
    }

    public function getArrivalYear(): ?int
    {
        return $this->arrivalYear;
    }

    public function setArrivalYear(int $arrivalYear): self
    {
        $this->arrivalYear = $arrivalYear;

        return $this;
    }

    public function getArrivalHour(): ?int
    {
        return $this->arrivalHour;
    }

    public function setArrivalHour(int $arrivalHour): self
    {
        $this->arrivalHour = $arrivalHour;

        return $this;
    }

    public function getArrivalMinute(): ?int
    {
        return $this->arrivalMinute;
    }

    public function setArrivalMinute(int $arrivalMinute): self
    {
        $this->arrivalMinute = $arrivalMinute;

        return $this;
    }

    public function getRequiresResus(): ?bool
    {
        return $this->requiresResus;
    }

    public function setRequiresResus(bool $requiresResus): self
    {
        $this->requiresResus = $requiresResus;

        return $this;
    }

    public function getRequiresCathlab(): ?bool
    {
        return $this->requiresCathlab;
    }

    public function setRequiresCathlab(bool $requiresCathlab): self
    {
        $this->requiresCathlab = $requiresCathlab;

        return $this;
    }

    public function getOccasion(): ?string
    {
        return $this->occasion;
    }

    public function setOccasion(?string $occasion): self
    {
        $this->occasion = $occasion;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getIsCPR(): ?bool
    {
        return $this->isCPR;
    }

    public function setIsCPR(bool $isCPR): self
    {
        $this->isCPR = $isCPR;

        return $this;
    }

    public function getIsVentilated(): ?bool
    {
        return $this->isVentilated;
    }

    public function setIsVentilated(bool $isVentilated): self
    {
        $this->isVentilated = $isVentilated;

        return $this;
    }

    public function getIsShock(): ?bool
    {
        return $this->isShock;
    }

    public function setIsShock(bool $isShock): self
    {
        $this->isShock = $isShock;

        return $this;
    }

    public function getIsInfectious(): ?string
    {
        return $this->isInfectious;
    }

    public function setIsInfectious(string $isInfectious): self
    {
        $this->isInfectious = $isInfectious;

        return $this;
    }

    public function getIsPregnant(): ?bool
    {
        return $this->isPregnant;
    }

    public function setIsPregnant(bool $isPregnant): self
    {
        $this->isPregnant = $isPregnant;

        return $this;
    }

    public function getIsWithPhysician(): ?bool
    {
        return $this->isWithPhysician;
    }

    public function setIsWithPhysician(bool $isWithPhysician): self
    {
        $this->isWithPhysician = $isWithPhysician;

        return $this;
    }

    public function getAssignment(): ?string
    {
        return $this->assignment;
    }

    public function setAssignment(?string $assignment): self
    {
        $this->assignment = $assignment;

        return $this;
    }

    public function getModeOfTransport(): ?string
    {
        return $this->modeOfTransport;
    }

    public function setModeOfTransport(string $modeOfTransport): self
    {
        $this->modeOfTransport = $modeOfTransport;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getSpeciality(): ?string
    {
        return $this->speciality;
    }

    public function setSpeciality(string $speciality): self
    {
        $this->speciality = $speciality;

        return $this;
    }

    public function getSpecialityDetail(): ?string
    {
        return $this->specialityDetail;
    }

    public function setSpecialityDetail(string $specialityDetail): self
    {
        $this->specialityDetail = $specialityDetail;

        return $this;
    }

    public function getHandoverPoint(): ?string
    {
        return $this->handoverPoint;
    }

    public function setHandoverPoint(string $handoverPoint): self
    {
        $this->handoverPoint = $handoverPoint;

        return $this;
    }

    public function getSpecialityWasClosed(): ?bool
    {
        return $this->specialityWasClosed;
    }

    public function setSpecialityWasClosed(bool $specialityWasClosed): self
    {
        $this->specialityWasClosed = $specialityWasClosed;

        return $this;
    }

    public function getPZC(): ?int
    {
        return $this->PZC;
    }

    public function setPZC(int $PZC): self
    {
        $this->PZC = $PZC;

        return $this;
    }

    public function getPZCText(): ?string
    {
        return $this->PZCText;
    }

    public function setPZCText(string $PZCText): self
    {
        $this->PZCText = $PZCText;

        return $this;
    }

    public function getSecondaryPZC(): ?int
    {
        return $this->SecondaryPZC;
    }

    public function setSecondaryPZC(?int $SecondaryPZC): self
    {
        $this->SecondaryPZC = $SecondaryPZC;

        return $this;
    }

    public function getSecondaryPZCText(): ?string
    {
        return $this->SecondaryPZCText;
    }

    public function setSecondaryPZCText(string $SecondaryPZCText): self
    {
        $this->SecondaryPZCText = $SecondaryPZCText;

        return $this;
    }
}
