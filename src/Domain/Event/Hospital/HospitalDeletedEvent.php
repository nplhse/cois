<?php

declare(strict_types=1);

namespace Domain\Event\Hospital;

use Domain\Contracts\DomainEventInterface;
use Domain\Contracts\HospitalInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class HospitalDeletedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'hospital.deleted';

    public function __construct(
        private HospitalInterface $hospital
    ) {
    }

    public function getHospital(): HospitalInterface
    {
        return $this->hospital;
    }
}
