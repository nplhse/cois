<?php

namespace App\Service;

use App\DataTransferObjects\AgeStatistics;
use App\DataTransferObjects\GenderStatistics;
use App\Repository\AllocationRepository;

class StatisticsService
{
    private AllocationRepository $allocationRepository;

    private const PRECISION = 2;

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
                $genderStatistics->setMalePercent($this->getValueInPercent($item['counter']));
            }

            if ('W' === $item['gender']) {
                $genderStatistics->setFemaleCount($item['counter']);
                $genderStatistics->setFemalePercent($this->getValueInPercent($item['counter']));
            }

            if ('D' === $item['gender']) {
                $genderStatistics->setOtherCount($item['counter']);
                $genderStatistics->setOtherPercent($this->getValueInPercent($item['counter']));
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

    public function getScaleForXAxis(int $maxValue, int $n = 5): array
    {
        $scale = [];
        $i = 0;

        for ($i = 0; $i <= $maxValue; ++$i) {
            if (0 === $i % $n) {
                $scale[$i] = $i;
            } else {
                $scale[$i] = null;
            }
        }

        return $scale;
    }

    private function getValueInPercent(int $value): float
    {
        return round(($value / $this->total) * 100, 2);
    }
}
