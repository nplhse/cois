<?php

namespace App\DataTransferObjects;

class AllocationStatistics
{
    private array $RMIs = [];

    private array $specialities = [];

    private array $specialityDetails = [];

    private array $SK = [];

    private array $requiresResus;

    private array $requiresCathlab;

    private array $isCPR;

    private array $isVentilated;

    private array $isShock;

    private array $isInfectious;

    private array $isPregnant;

    private array $isWithPhysician;

    private array $isWorkAccident;

    public function getSK(): array
    {
        return $this->SK;
    }

    public function setSK(array $SK): void
    {
        $this->SK = $SK;
    }

    public function setRMIs(array $RMIs): void
    {
        $this->RMIs = $RMIs;
    }

    public function getRMIs(): array
    {
        return $this->RMIs;
    }

    public function getSpecialities(): array
    {
        return $this->specialities;
    }

    public function setSpecialities(array $specialities): void
    {
        $this->specialities = $specialities;
    }

    public function getSpecialityDetails(): array
    {
        return $this->specialityDetails;
    }

    public function setSpecialityDetails(array $specialityDetails): void
    {
        $this->specialityDetails = $specialityDetails;
    }

    public function getRequiresResus(): array
    {
        return $this->requiresResus;
    }

    public function setRequiresResus(array $requiresResus): void
    {
        $this->requiresResus = $requiresResus;
    }

    public function getRequiresCathlab(): array
    {
        return $this->requiresCathlab;
    }

    public function setRequiresCathlab(array $requiresCathlab): void
    {
        $this->requiresCathlab = $requiresCathlab;
    }

    public function getIsCPR(): array
    {
        return $this->isCPR;
    }

    public function setIsCPR(array $isCPR): void
    {
        $this->isCPR = $isCPR;
    }

    public function getIsVentilated(): array
    {
        return $this->isVentilated;
    }

    public function setIsVentilated(array $isVentilated): void
    {
        $this->isVentilated = $isVentilated;
    }

    public function getIsShock(): array
    {
        return $this->isShock;
    }

    public function setIsShock(array $isShock): void
    {
        $this->isShock = $isShock;
    }

    public function getIsInfectious(): array
    {
        return $this->isInfectious;
    }

    public function setIsInfectious(array $isInfectious): void
    {
        $this->isInfectious = $isInfectious;
    }

    public function getIsPregnant(): array
    {
        return $this->isPregnant;
    }

    public function setIsPregnant(array $isPregnant): void
    {
        $this->isPregnant = $isPregnant;
    }

    public function getIsWithPhysician(): array
    {
        return $this->isWithPhysician;
    }

    public function setIsWithPhysician(array $isWithPhysician): void
    {
        $this->isWithPhysician = $isWithPhysician;
    }

    public function getIsWorkAccident(): array
    {
        return $this->isWorkAccident;
    }

    public function setIsWorkAccident(array $isWorkAccident): void
    {
        $this->isWorkAccident = $isWorkAccident;
    }
}
