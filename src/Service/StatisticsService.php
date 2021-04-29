<?php

namespace App\Service;

use App\DataTransferObjects\AgeStatistics;
use App\DataTransferObjects\GenderStatistics;
use App\DataTransferObjects\TimeStatistics;
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

    public function generateTimeStats(): TimeStatistics
    {
        $timeStatistics = new TimeStatistics();

        $stats = $this->allocationRepository->countAllocationsByTime();

        foreach ($stats as $item) {
            $timeStatistics->setTimeOfDay($item['arrivalHour'], $item['counter']);
        }

        $i = 0;
        $ages = [];

        while ($i <= 23) {
            $times[$i] = $timeStatistics->getTimeOfDay($i);
            ++$i;
        }

        $timeStatistics->setTimesOfDay($times);

        $stats = $this->allocationRepository->countAllocationsByWeekday();

        $weekdays = [];

        foreach ($stats as $item) {
            $weekdays[$item['arrivalWeekday']] = $item['counter'];
        }

        $sorted_weekdays = [];
        $sorted_weekdays[0] = $weekdays['Montag'];
        $sorted_weekdays[1] = $weekdays['Dienstag'];
        $sorted_weekdays[2] = $weekdays['Mittwoch'];
        $sorted_weekdays[3] = $weekdays['Donnerstag'];
        $sorted_weekdays[4] = $weekdays['Freitag'];
        $sorted_weekdays[5] = $weekdays['Samstag'];
        $sorted_weekdays[6] = $weekdays['Sonntag'];

        $timeStatistics->setWeekdays($sorted_weekdays);

        return $timeStatistics;
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
