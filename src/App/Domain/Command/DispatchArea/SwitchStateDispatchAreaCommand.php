<?php

declare(strict_types=1);

namespace App\Domain\Command\DispatchArea;

class SwitchStateDispatchAreaCommand
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
