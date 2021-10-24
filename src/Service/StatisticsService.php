<?php

namespace App\Service;

class StatisticsService
{
    public const VALUE_PRECISION = 2;

    public function generateDayResults(object $allocations): array
    {
        $weekdays = [];

        foreach ($allocations->getItems() as $allocation) {
            $weekdays[$allocation->getDay()] = [
                'day' => $allocation->getDay(),
                'count' => $allocation->getCounter(),
            ];
        }

        $results = [];
        $results[0] = $weekdays['Montag'];
        $results[1] = $weekdays['Dienstag'];
        $results[2] = $weekdays['Mittwoch'];
        $results[3] = $weekdays['Donnerstag'];
        $results[4] = $weekdays['Freitag'];
        $results[5] = $weekdays['Samstag'];
        $results[6] = $weekdays['Sonntag'];

        return $results;
    }

    public function generateGenderResults(object $allocations): array
    {
        $results = [];
        $total = 0;

        foreach ($allocations->getItems() as $allocation) {
            $total = $total + $allocation->getCounter();
        }

        foreach ($allocations->getItems() as $allocation) {
            if ('M' == $allocation->getGender()) {
                $gender = 'male';
            } elseif ('W' == $allocation->getGender()) {
                $gender = 'female';
            } else {
                $gender = 'other';
            }

            $percent = $this->getFormattedNumber($this->getValueInPercent($allocation->getCounter(), $total)).'%';

            $results[] = [
                'label' => $gender,
                'count' => $allocation->getCounter(),
                'percent' => $percent,
            ];
        }

        return $results;
    }

    public function generateTimeResults(object $allocations): array
    {
        $results = [];

        foreach ($allocations->getItems() as $allocation) {
            $results[] = [
                'time' => $allocation->getTime(),
                'count' => $allocation->getCounter(),
            ];
        }

        return $results;
    }

    private function getValueInPercent(int $value, int $total): float
    {
        return round(($value / $total) * 100, self::VALUE_PRECISION);
    }

    private function getFormattedNumber(float $value): float
    {
        return (float) number_format($value, 2, ',', '.');
    }
}
