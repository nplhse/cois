<?php

namespace App\DataTransferObjects;

class AllocationStatistics
{
    private array $RMIs = [];

    private array $specialities = [];

    private array $specialityDetails = [];

    private array $SK = [];

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
}
