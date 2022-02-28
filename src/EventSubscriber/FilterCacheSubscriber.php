<?php

namespace App\EventSubscriber;

use App\Domain\Event\DispatchArea\DispatchAreaCreatedEvent;
use App\Domain\Event\DispatchArea\DispatchAreaDeletedEvent;
use App\Domain\Event\DispatchArea\DispatchAreaUpdatedEvent;
use App\Domain\Event\Hospital\HospitalCreatedEvent;
use App\Domain\Event\Hospital\HospitalDeletedEvent;
use App\Domain\Event\Hospital\HospitalEditedEvent;
use App\Domain\Event\State\StateCreatedEvent;
use App\Domain\Event\State\StateDeletedEvent;
use App\Domain\Event\State\StateUpdatedEvent;
use App\Domain\Event\SupplyArea\SupplyAreaCreatedEvent;
use App\Domain\Event\SupplyArea\SupplyAreaDeletedEvent;
use App\Domain\Event\SupplyArea\SupplyAreaUpdatedEvent;
use App\Domain\Event\User\UserChangedUsernameEvent;
use App\Domain\Event\User\UserCreatedEvent;
use App\Domain\Event\User\UserRegisteredEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class FilterCacheSubscriber implements EventSubscriberInterface
{
    private TagAwareCacheInterface $cache;

    public function __construct(TagAwareCacheInterface $appCache)
    {
        $this->cache = $appCache;
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

    public static function getSubscribedEvents()
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
