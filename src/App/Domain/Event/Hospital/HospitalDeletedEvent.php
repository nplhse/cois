<?php

declare(strict_types=1);

namespace App\Domain\Event\Hospital;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\HospitalInterface;
use App\Domain\Event\NamedEventTrait;
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
