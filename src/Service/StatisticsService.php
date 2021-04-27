<?php

namespace App\Service;

use App\DataTransferObjects\AgeStatistics;
use App\DataTransferObjects\GenderStatistics;
use App\Repository\AllocationRepository;

class StatisticsService
{
    private AllocationRepository $allocationRepository;

    private int $total;

    public function __construct(AllocationRepository $allocationRepository)
    {
        $this->allocationRepository = $allocationRepository;

        $this->total = (int) $this->allocationRepository->countAllocations();
    }

    public function generateGenderStats(): GenderStatistics
    {
        $genderStatistics = new GenderStatistics();

        $stats = $this->allocationRepository->countAllocationsByGender();

        foreach ($stats as $item) {
            if ('M' === $item['gender']) {
                $genderStatistics->setMaleCount($item['counter']);
                $genderStatistics->setMalePercent(($item['counter'] / $this->total) * 100);
            }

            if ('W' === $item['gender']) {
                $genderStatistics->setFemaleCount($item['counter']);
                $genderStatistics->setFemalePercent(($item['counter'] / $this->total) * 100);
            }

            if ('D' === $item['gender']) {
                $genderStatistics->setOtherCount($item['counter']);
                $genderStatistics->setOtherPercent(($item['counter'] / $this->total) * 100);
            }
        }

        return $genderStatistics;
    }

    public function generateAgeStats(): AgeStatistics
    {
        $ageStatistics = new AgeStatistics();

        $stats = $this->allocationRepository->countAllocationsByAge();

        $i = 0;
        $length = count($stats) - 1;

        foreach ($stats as $item) {
            $ageStatistics->setAge($item['age'], $item['counter']);

            if ($i === $length) {
                $ageStatistics->setMaxAge($item['age']);
            }

            ++$i;
        }

        $i = 0;
        $ages = [];

        while ($i <= $ageStatistics->getMaxAge()) {
            $ages[$i] = $ageStatistics->getAge($i);
            ++$i;
        }

        $ageStatistics->setAges($ages);

        return $ageStatistics;
    }
}
