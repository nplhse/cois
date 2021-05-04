<?php


namespace App\DataTransferObjects;


class AllocationStatistics
{
    private $RMIs = [];

    private $specialities = [];

    private $specialityDetails = [];

    private $SK = [];

    /**
     * @return array
     */
    public function getSK(): array
    {
        return $this->SK;
    }

    /**
     * @param array $SK
     */
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

    /**
     * @return array
     */
    public function getSpecialities(): array
    {
        return $this->specialities;
    }

    /**
     * @param array $specialities
     */
    public function setSpecialities(array $specialities): void
    {
        $this->specialities = $specialities;
    }

    /**
     * @return array
     */
    public function getSpecialityDetails(): array
    {
        return $this->specialityDetails;
    }

    /**
     * @param array $specialityDetails
     */
    public function setSpecialityDetails(array $specialityDetails): void
    {
        $this->specialityDetails = $specialityDetails;
    }
}
