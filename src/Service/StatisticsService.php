<?php

namespace App\Service;

use App\Repository\AllocationRepository;

class StatisticsService
{
    private AllocationRepository $allocationRepository;

    private int $total;

    public function __construct(AllocationRepository $allocationRepository)
    {
        $this->allocationRepository = $allocationRepository;

        $this->total = $this->allocationRepository->countAllocations();
    }

    public function generateAgeStats(): array
    {
        return $this->allocationRepository->countAllocationsByAge();
    }

    public function generateGenderStats(): array
    {
        return $this->allocationRepository->countAllocationsByGender();
    }
}
