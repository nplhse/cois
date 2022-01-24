<?php

namespace App\Domain\Command\SupplyArea;

class SwitchStateSupplyAreaCommand
{
    private int $areaId;

    private int $stateId;

    public function __construct(int $areaId, int $stateId)
    {
        $this->areaId = $areaId;
        $this->stateId = $stateId;
    }

    public function getAreaId(): int
    {
        return $this->areaId;
    }

    public function getStateId(): int
    {
        return $this->stateId;
    }
}
