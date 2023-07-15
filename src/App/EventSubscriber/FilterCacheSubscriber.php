<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Domain\Event\DispatchArea\DispatchAreaCreatedEvent;
use Domain\Event\DispatchArea\DispatchAreaDeletedEvent;
use Domain\Event\DispatchArea\DispatchAreaUpdatedEvent;
use Domain\Event\Hospital\HospitalCreatedEvent;
use Domain\Event\Hospital\HospitalDeletedEvent;
use Domain\Event\Hospital\HospitalEditedEvent;
use Domain\Event\State\StateCreatedEvent;
use Domain\Event\State\StateDeletedEvent;
use Domain\Event\State\StateUpdatedEvent;
use Domain\Event\SupplyArea\SupplyAreaCreatedEvent;
use Domain\Event\SupplyArea\SupplyAreaDeletedEvent;
use Domain\Event\SupplyArea\SupplyAreaUpdatedEvent;
use Domain\Event\User\UserChangedUsernameEvent;
use Domain\Event\User\UserCreatedEvent;
use Domain\Event\User\UserRegisteredEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class FilterCacheSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TagAwareCacheInterface $cache
    ) {
    }

    public function onUserChange(UserRegisteredEvent|UserCreatedEvent|UserChangedUsernameEvent $event): void
    {
        $this->cache->invalidateTags(['user_filter']);
    }

    public function onStateChange(StateCreatedEvent|StateUpdatedEvent|StateDeletedEvent $event): void
    {
        $this->cache->invalidateTags(['state_filter']);
    }

    public function onHospitalChange(HospitalCreatedEvent|HospitalEditedEvent|HospitalDeletedEvent $event): void
    {
        $this->cache->invalidateTags(['hospital_filter']);
    }

    public function onDispatchAreaChange(DispatchAreaCreatedEvent|DispatchAreaUpdatedEvent|DispatchAreaDeletedEvent $event): void
    {
        $this->cache->invalidateTags(['dispatch_area_filter']);
    }

    public function onSupplyAreaChange(SupplyAreaCreatedEvent|SupplyAreaUpdatedEvent|SupplyAreaDeletedEvent $event): void
    {
        $this->cache->invalidateTags(['supply_area_filter']);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::NAME => ['onUserChange', 0],
            UserCreatedEvent::NAME => ['onUserChange', 0],
            UserChangedUsernameEvent::NAME => ['onUserChange', 0],
            StateCreatedEvent::NAME => ['onStateChange', 0],
            StateUpdatedEvent::NAME => ['onStateChange', 0],
            StateDeletedEvent::NAME => ['onStateChange', 0],
            HospitalCreatedEvent::NAME => ['onHospitalChange', 0],
            HospitalEditedEvent::NAME => ['onHospitalChange', 0],
            HospitalDeletedEvent::NAME => ['onHospitalChange', 0],
            DispatchAreaCreatedEvent::NAME => ['onDispatchAreaChange', 0],
            DispatchAreaUpdatedEvent::NAME => ['onDispatchAreaChange', 0],
            DispatchAreaDeletedEvent::NAME => ['onDispatchAreaChange', 0],
            SupplyAreaCreatedEvent::NAME => ['onSupplyAreaChange', 0],
            SupplyAreaUpdatedEvent::NAME => ['onSupplyAreaChange', 0],
            SupplyAreaDeletedEvent::NAME => ['onSupplyAreaChange', 0],
        ];
    }
}
