<?php

namespace App\Domain\Command\SupplyArea;

class SwitchStateSupplyAreaCommand
{
    public function __construct(
        private int $areaId,
        private int $stateId
    ) {
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
