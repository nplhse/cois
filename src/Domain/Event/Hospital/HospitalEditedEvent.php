<?php

declare(strict_types=1);

namespace App\Domain\Event\Hospital;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\HospitalInterface;
use App\Domain\Event\Traits\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class HospitalEditedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'hospital.edited';

    private HospitalInterface $hospital;

    public function __construct(HospitalInterface $hospital)
    {
        $this->hospital = $hospital;
    }

    public function getHospital(): HospitalInterface
    {
        return $this->hospital;
    }
}
