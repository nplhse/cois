<?php

namespace App\DataTransferObjects;

class TimeStatistics
{
    private array $timesOfDay;

    private array $weekdays;

    public function getTimesOfDay(): array
    {
        return $this->timesOfDay;
    }

    public function setTimesOfDay(array $timesOfDay): void
    {
        $this->timesOfDay = $timesOfDay;
    }

    public function getTimeOfDay(int $time): int
    {
        if (!isset($this->timesOfDay[$time])) {
            return 0;
        }

        return $this->timesOfDay[$time];
    }

    public function setTimeOfDay(int $time, int $value): void
    {
        $this->timesOfDay[$time] = $value;
    }

    public function getWeekdays(): array
    {
        return $this->weekdays;
    }

    public function setWeekdays(array $weekdays): void
    {
        $this->weekdays = $weekdays;
    }

    public function getWeekday(string $day): int
    {
        if (isset($this->weekdays[$day])) {
            return $this->weekdays[$day];
        }

        return 0;
    }
}
