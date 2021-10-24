<?php

namespace App\Service;

class StatisticsService
{
    public const VALUE_PRECISION = 2;

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

            $results[] = [
                'label' => $gender,
                'count' => $allocation->getCounter(),
                'percent' => $this->getFormattedNumber($this->getValueInPercent($allocation->getCounter(), $total)).'%',
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
