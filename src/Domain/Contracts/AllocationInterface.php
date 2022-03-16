<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

interface AllocationInterface
{
    public function setHospital(HospitalInterface $hospital): self;

    public function getHospital(): HospitalInterface;

    public function setImport(ImportInterface $import): self;

    public function getImport(): ImportInterface;

    public function setState(StateInterface $state): self;

    public function getState(): StateInterface;

    public function setDispatchArea(DispatchAreaInterface $dispatchArea): self;

    public function getDispatchArea(): DispatchAreaInterface;

    public function setSupplyArea(?SupplyAreaInterface $supplyArea): self;

    public function getSupplyArea(): ?SupplyAreaInterface;

    public function setCreatedAt(\DateTimeInterface $createdAt): self;

    public function getCreatedAt(): \DateTimeInterface;

    public function getCreationDate(): string;

    public function getCreationTime(): string;

    public function getCreationYear(): int;

    public function getCreationMonth(): int;

    public function getCreationDay(): int;

    public function getCreationWeekday(): int;

    public function getCreationHour(): int;

    public function getCreationMinute(): int;

    public function setArrivalAt(\DateTimeInterface $arrivalAt): self;

    public function getArrivalAt(): \DateTimeInterface;

    public function getArrivalDate(): string;

    public function getArrivalTime(): string;

    public function getArrivalYear(): int;

    public function getArrivalMonth(): int;

    public function getArrivalDay(): int;

    public function getArrivalWeekday(): int;

    public function getArrivalHour(): int;

    public function getArrivalMinute(): int;

    public function setUrgency(int $urgency): self;

    public function getUrgency(): int;

    public function setOccasion(string $occasion): self;

    public function getOccasion(): string;

    public function setAssignment(string $assignment): self;

    public function getAssignment(): string;

    public function setRequiresResus(bool $requiresResus): self;

    public function getRequiresResus(): bool;

    public function setRequiresCathlab(bool $requiresCathlab): self;

    public function getRequiresCathlab(): bool;

    public function setGender(string $gender): self;

    public function getGender(): string;

    public function setAge(int $age): self;

    public function getAge(): int;

    public function setIsCPR(bool $isCPR): self;

    public function getIsCPR(): bool;

    public function setIsVentilated(bool $isVentilated): self;

    public function getIsVentilated(): bool;

    public function setIsShock(bool $isShock): self;

    public function getIsShock(): bool;

    public function setIsInfectious(string $isInfectious): self;

    public function getIsInfectious(): string;

    public function setIsPregnant(bool $isPregnant): self;

    public function getIsPregnant(): bool;

    public function setIsWorkAccident(bool $isWorkAccident): self;

    public function getIsWorkAccident(): bool;

    public function setIsWithPhysician(bool $isWithPhysician): self;

    public function getIsWithPhysician(): bool;

    public function setModeOfTransport(string $modeOfTransport): self;

    public function getModeOfTransport(): string;

    public function setSpeciality(string $speciality): self;

    public function getSpeciality(): string;

    public function setSpecialityDetail(string $specialityDetail): self;

    public function getSpecialityDetail(): string;

    public function setSpecialityWasClosed(bool $specialityWasClosed): self;

    public function getSpecialityWasClosed(): bool;

    public function setHandoverPoint(string $handoverPoint): self;

    public function getHandoverPoint(): ?string;

    public function setComment(?string $comment): self;

    public function getComment(): ?string;

    public function setIndication(string $indication): self;

    public function getIndication(): string;

    public function setIndicationCode(int $indicationCode): self;

    public function getIndicationCode(): int;

    public function setSecondaryIndication(?string $secondaryIndication): self;

    public function getSecondaryIndication(): ?string;

    public function setSecondaryIndicationCode(?int $secondaryIndicationCode): self;

    public function getSecondaryIndicationCode(): ?int;

    public function setSecondaryDeployment(?string $secondaryDeployment): self;

    public function getSecondaryDeployment(): ?string;
}
