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

    public function generateAgeResults(object $allocations): array
    {
        $results = [];
        $total = 0;
        $max = 0;

        foreach ($allocations->getItems() as $allocation) {
            $total = $total + $allocation['counter'];
            if ($allocation['age'] > $max) {
                $max = $allocation['age'];
            }
        }

        $temp = [];

        for ($i = 0; $i <= $max; ++$i) {
            $temp[$i] = [];
            $temp[$i]['age'] = $i;
            $temp[$i]['male'] = 0;
            $temp[$i]['female'] = 0;
            $temp[$i]['other'] = 0;
        }

        foreach ($allocations->getItems() as $allocation) {
            if ('M' == $allocation['gender']) {
                $temp[$allocation['age']]['male'] = $allocation['counter'];
            } elseif ('W' == $allocation['gender']) {
                $temp[$allocation['age']]['female'] = $allocation['counter'];
            } else {
                $temp[$allocation['age']]['other'] = $allocation['counter'];
            }
        }

        foreach ($temp as $row) {
            $results[] = [
                'age' => $row['age'],
                'male' => $row['male'],
                'female' => $row['female'],
                'other' => $row['other'],
            ];
        }

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
                $gender = 'Male';
            } elseif ('W' == $allocation->getGender()) {
                $gender = 'Female';
            } else {
                $gender = 'Other';
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

    public function generateAssignmentResults(object $allocations): array
    {
        $results = [];
        $total = 0;

        foreach ($allocations->getItems() as $allocation) {
            $total = $total + $allocation->getCounter();
        }

        foreach ($allocations->getItems() as $allocation) {
            if (0 === $allocation->getAssignment()) {
                break;
            }

            // For SVG creation, only returning percentage number
            $percent = $this->getValueInPercent($allocation->getCounter(), $total);

            $results[] = [
                'infection' => $allocation->getAssignment(),
                'count' => $allocation->getCounter(),
                'percent' => $percent,
                'label' => $allocation->getAssignment(),
            ];
        }

        return $results;
    }

    public function generateInfectionResults(object $allocations): array
    {
        $results = [];
        $total = 0;

        foreach ($allocations->getItems() as $allocation) {
            $total = $total + $allocation->getCounter();
        }

        foreach ($allocations->getItems() as $allocation) {
            if (0 === $allocation->getInfection()) {
                break;
            }

            // For SVG creation, only returning percentage number
            $percent = $this->getValueInPercent($allocation->getCounter(), $total);

            $results[] = [
                'infection' => $allocation->getInfection(),
                'count' => $allocation->getCounter(),
                'percent' => $percent,
                'label' => $allocation->getInfection(),
            ];
        }

        return $results;
    }

    public function generateOccasionResults(object $allocations): array
    {
        $results = [];
        $total = 0;

        foreach ($allocations->getItems() as $allocation) {
            $total = $total + $allocation->getCounter();
        }

        foreach ($allocations->getItems() as $allocation) {
            if (0 === $allocation->getOccasion()) {
                break;
            }

            // For SVG creation, only returning percentage number
            $percent = $this->getValueInPercent($allocation->getCounter(), $total);

            $results[] = [
                'infection' => $allocation->getOccasion(),
                'count' => $allocation->getCounter(),
                'percent' => $percent,
                'label' => $allocation->getOccasion(),
            ];
        }

        return $results;
    }

    public function generateSpecialityResults(object $allocations): array
    {
        $results = [];
        $total = 0;

        foreach ($allocations->getItems() as $allocation) {
            $total = $total + $allocation->getCounter();
        }

        foreach ($allocations->getItems() as $allocation) {
            if (0 === $allocation->getSpeciality()) {
                break;
            }

            // For SVG creation, only returning percentage number
            $percent = $this->getValueInPercent($allocation->getCounter(), $total);

            $results[] = [
                'speciality' => $allocation->getSpeciality(),
                'count' => $allocation->getCounter(),
                'percent' => $percent,
                'label' => $allocation->getSpeciality(),
            ];
        }

        return $results;
    }

    public function generatePZCResults(object $allocations): array
    {
        $results = [];
        $total = 0;

        foreach ($allocations->getItems() as $allocation) {
            $total = $total + $allocation->getCounter();
        }

        foreach ($allocations->getItems() as $allocation) {
            if (0 === $allocation->getPZC()) {
                break;
            }

            // For SVG creation, only returning percentage number
            $percent = $this->getValueInPercent($allocation->getCounter(), $total);

            $results[] = [
                'PZC' => $allocation->getPZC(),
                'count' => $allocation->getCounter(),
                'percent' => $percent,
                'label' => $allocation->getPZC().' '.$allocation->getPZCText(),
            ];
        }

        return $results;
    }

    public function generateUrgencyResults(object $allocations): array
    {
        $results = [];
        $total = 0;

        foreach ($allocations->getItems() as $allocation) {
            $total = $total + $allocation->getCounter();
        }

        foreach ($allocations->getItems() as $allocation) {
            if (preg_match('/(?P<SK>\w+)(?P<Count>\d+)/', $allocation->getUrgency())) {
                $percent = $this->getFormattedNumber($this->getValueInPercent($allocation->getCounter(), $total)).'%';

                $results[] = [
                    'label' => $allocation->getUrgency(),
                    'count' => $allocation->getCounter(),
                    'percent' => $percent,
                ];
            }
        }

        return $results;
    }

    private function getValueInPercent(int $value, int $total): float
    {
        return round(($value / $total) * 100, self::VALUE_PRECISION);
    }

    private function getFormattedNumber(float $value): string
    {
        return number_format($value, self::VALUE_PRECISION, ',', '.');
    }
}
