<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contracts\AllocationInterface;
use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\IdentifierInterface;
use App\Domain\Contracts\ImportInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Entity\Traits\IdentifierTrait;

class Allocation implements AllocationInterface, IdentifierInterface
{
    use IdentifierTrait;

    public const GENDER_MALE = 'M';

    public const GENDER_FEMALE = 'W';

    public const GENDER_OTHER = 'D';

    private HospitalInterface $hospital;

    private ImportInterface $import;

    private StateInterface $state;

    private DispatchAreaInterface $dispatchArea;

    private ?SupplyAreaInterface $supplyArea = null;

    private \DateTimeInterface $createdAt;

    private string $creationDate;

    private string $creationTime;

    private int $creationYear;

    private int $creationMonth;

    private int $creationDay;

    private int $creationWeekday;

    private int $creationHour;

    private int $creationMinute;

    private \DateTimeInterface $arrivalAt;

    private string $arrivalDate;

    private string $arrivalTime;

    private int $arrivalYear;

    private int $arrivalMonth;

    private int $arrivalDay;

    private int $arrivalWeekday;

    private int $arrivalHour;

    private int $arrivalMinute;

    private int $urgency;

    private string $occasion;

    private string $assignment;

    private bool $requiresResus;

    private bool $requiresCathlab;

    private array $genders = [self::GENDER_MALE, self::GENDER_FEMALE, self::GENDER_OTHER];

    private string $gender;

    private int $age;

    private bool $isCPR;

    private bool $isVentilted;

    private bool $isShock;

    private string $isInfectious;

    private bool $isPregnant;

    private bool $isWorkAccident;

    private bool $isWithPhysician;

    private string $modeOfTransport;

    private string $speciality;

    private string $specialityDetail;

    private bool $specialityWasClosed;

    private string $handoverPoint;

    private ?string $comment;

    private string $indication;

    private int $indicationCode;

    private ?string $secondaryIndication;

    private ?int $secondaryIndicationCode;

    public function __construct()
    {
        $this->comment = null;
        $this->secondaryIndication = null;
        $this->secondaryIndicationCode = null;
    }

    public function setHospital(HospitalInterface $hospital): self
    {
        $this->hospital = $hospital;

        return $this;
    }

    public function getHospital(): HospitalInterface
    {
        return $this->hospital;
    }

    public function setImport(ImportInterface $import): self
    {
        $this->import = $import;

        return $this;
    }

    public function getImport(): ImportInterface
    {
        return $this->import;
    }

    public function setState(StateInterface $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getState(): StateInterface
    {
        return $this->state;
    }

    public function setDispatchArea(DispatchAreaInterface $dispatchArea): self
    {
        $this->dispatchArea = $dispatchArea;

        return $this;
    }

    public function getDispatchArea(): DispatchAreaInterface
    {
        return $this->dispatchArea;
    }

    public function setSupplyArea(?SupplyAreaInterface $supplyArea): self
    {
        $this->supplyArea = $supplyArea;

        return $this;
    }

    public function getSupplyArea(): ?SupplyAreaInterface
    {
        return $this->supplyArea;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        $this->creationDate = $createdAt->format('d.m.Y');
        $this->creationTime = $createdAt->format('H:i');
        $this->creationYear = (int) $createdAt->format('Y');
        $this->creationMonth = (int) $createdAt->format('m');
        $this->creationDay = (int) $createdAt->format('d');
        $this->creationWeekday = (int) $createdAt->format('N');
        $this->creationHour = (int) $createdAt->format('H');
        $this->creationMinute = (int) $createdAt->format('i');

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getCreationDate(): string
    {
        return $this->creationDate;
    }

    public function getCreationTime(): string
    {
        return $this->creationTime;
    }

    public function getCreationYear(): int
    {
        return $this->creationYear;
    }

    public function getCreationMonth(): int
    {
        return $this->creationMonth;
    }

    public function getCreationDay(): int
    {
        return $this->creationDay;
    }

    public function getCreationWeekday(): int
    {
        return $this->creationWeekday;
    }

    public function getCreationHour(): int
    {
        return $this->creationHour;
    }

    public function getCreationMinute(): int
    {
        return $this->creationMinute;
    }

    public function setArrivalAt(\DateTimeInterface $arrivalAt): self
    {
        $this->arrivalAt = $arrivalAt;

        $this->arrivalDate = $arrivalAt->format('d.m.Y');
        $this->arrivalTime = $arrivalAt->format('H:i');
        $this->arrivalYear = (int) $arrivalAt->format('Y');
        $this->arrivalMonth = (int) $arrivalAt->format('m');
        $this->arrivalDay = (int) $arrivalAt->format('d');
        $this->arrivalWeekday = (int) $arrivalAt->format('N');
        $this->arrivalHour = (int) $arrivalAt->format('H');
        $this->arrivalMinute = (int) $arrivalAt->format('i');

        return $this;
    }

    public function getArrivalAt(): \DateTimeInterface
    {
        return $this->arrivalAt;
    }

    public function getArrivalDate(): string
    {
        return $this->arrivalDate;
    }

    public function getArrivalTime(): string
    {
        return $this->arrivalTime;
    }

    public function getArrivalYear(): int
    {
        return $this->arrivalYear;
    }

    public function getArrivalMonth(): int
    {
        return $this->arrivalMonth;
    }

    public function getArrivalDay(): int
    {
        return $this->arrivalDay;
    }

    public function getArrivalWeekday(): int
    {
        return $this->arrivalWeekday;
    }

    public function getArrivalHour(): int
    {
        return $this->arrivalHour;
    }

    public function getArrivalMinute(): int
    {
        return $this->arrivalMinute;
    }

    public function setUrgency(int $urgency): self
    {
        if ($urgency >= 0 && $urgency <= 3) {
            $this->urgency = $urgency;

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('Urgency must be between 1 and 3, not %d', $urgency));
    }

    public function getUrgency(): int
    {
        return $this->urgency;
    }

    public function setOccasion(string $occasion): self
    {
        $this->occasion = $occasion;

        return $this;
    }

    public function getOccasion(): string
    {
        return $this->occasion;
    }

    public function setAssignment(string $assignment): self
    {
        $this->assignment = $assignment;

        return $this;
    }

    public function getAssignment(): string
    {
        return $this->assignment;
    }

    public function setRequiresResus(bool $requiresResus): self
    {
        $this->requiresResus = $requiresResus;

        return $this;
    }

    public function getRequiresResus(): bool
    {
        return $this->requiresResus;
    }

    public function setRequiresCathlab(bool $requiresCathlab): self
    {
        $this->requiresCathlab = $requiresCathlab;

        return $this;
    }

    public function getRequiresCathlab(): bool
    {
        return $this->requiresCathlab;
    }

    public function setGender(string $gender): self
    {
        if (in_array($gender, $this->genders, true)) {
            $this->gender = $gender;

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('Gender %s is not a valid option', $gender));
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setAge(int $age): self
    {
        if ($age >= 0) {
            $this->age = $age;

            return $this;
        }

        throw new \InvalidArgumentException('Age must not be below 0');
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setIsCPR(bool $isCPR): self
    {
        $this->isCPR = $isCPR;

        return $this;
    }

    public function getIsCPR(): bool
    {
        return $this->isCPR;
    }

    public function setIsVentilated(bool $isVentilated): self
    {
        $this->isVentilted = $isVentilated;

        return $this;
    }

    public function getIsVentilated(): bool
    {
        return $this->isVentilted;
    }

    public function setIsShock(bool $isShock): self
    {
        $this->isShock = $isShock;

        return $this;
    }

    public function getIsShock(): bool
    {
        return $this->isShock;
    }

    public function setIsInfectious(string $isInfectious): self
    {
        $this->isInfectious = $isInfectious;

        return $this;
    }

    public function getIsInfectious(): string
    {
        return $this->isInfectious;
    }

    public function setIsPregnant(bool $isPregnant): self
    {
        $this->isPregnant = $isPregnant;

        return $this;
    }

    public function getIsPregnant(): bool
    {
        return $this->isPregnant;
    }

    public function setIsWorkAccident(bool $isWorkAccident): self
    {
        $this->isWorkAccident = $isWorkAccident;

        return $this;
    }

    public function getIsWorkAccident(): bool
    {
        return $this->isWorkAccident;
    }

    public function setIsWithPhysician(bool $isWithPhysician): self
    {
        $this->isWithPhysician = $isWithPhysician;

        return $this;
    }

    public function getIsWithPhysician(): bool
    {
        return $this->isWithPhysician;
    }

    public function setModeOfTransport(string $modeOfTransport): self
    {
        $this->modeOfTransport = $modeOfTransport;

        return $this;
    }

    public function getModeOfTransport(): string
    {
        return $this->modeOfTransport;
    }

    public function setSpeciality(string $speciality): self
    {
        $this->speciality = $speciality;

        return $this;
    }

    public function getSpeciality(): string
    {
        return $this->speciality;
    }

    public function setSpecialityDetail(string $specialityDetail): self
    {
        $this->specialityDetail = $specialityDetail;

        return $this;
    }

    public function getSpecialityDetail(): string
    {
        return $this->specialityDetail;
    }

    public function setSpecialityWasClosed(bool $specialityWasClosed): self
    {
        $this->specialityWasClosed = $specialityWasClosed;

        return $this;
    }

    public function getSpecialityWasClosed(): bool
    {
        return $this->specialityWasClosed;
    }

    public function setHandoverPoint(string $handoverPoint): self
    {
        $this->handoverPoint = $handoverPoint;

        return $this;
    }

    public function getHandoverPoint(): string
    {
        return $this->handoverPoint;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setIndication(string $indication): self
    {
        $this->indication = $indication;

        return $this;
    }

    public function getIndication(): string
    {
        return $this->indication;
    }

    public function setIndicationCode(int $indicationCode): self
    {
        $this->indicationCode = $indicationCode;

        return $this;
    }

    public function getIndicationCode(): int
    {
        return $this->indicationCode;
    }

    public function setSecondaryIndication(?string $secondaryIndication): self
    {
        $this->secondaryIndication = $secondaryIndication;

        return $this;
    }

    public function getSecondaryIndication(): ?string
    {
        return $this->secondaryIndication;
    }

    public function setSecondaryIndicationCode(?int $secondaryIndicationCode): self
    {
        $this->secondaryIndicationCode = $secondaryIndicationCode;

        return $this;
    }

    public function getSecondaryIndicationCode(): ?int
    {
        return $this->secondaryIndicationCode;
    }
}
