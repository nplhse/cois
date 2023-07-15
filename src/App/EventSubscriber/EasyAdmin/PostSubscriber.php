<?php

declare(strict_types=1);

namespace App\EventSubscriber\EasyAdmin;

use App\Entity\Post;
use Domain\Contracts\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly Security $security
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setDetails'],
            BeforeEntityUpdatedEvent::class => ['updateDetails'],
        ];
    }

    public function setDetails(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Post)) {
            return;
        }

        $slug = $this->slugger->slug($entity->getTitle())->lower();
        $entity->setSlug($slug->toString());

        /** @var UserInterface $user */
        $user = $this->security->getUser();
        $entity->setCreatedBy($user);

        $entity->setCreatedAt(new \DateTimeImmutable());
    }

    public function updateDetails(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Post)) {
            return;
        }

        if (null === $entity->getSlug()) {
            $slug = $this->slugger->slug($entity->getTitle())->lower();
            $entity->setSlug($slug->toString());
        }

        /** @var UserInterface $user */
        $user = $this->security->getUser();
        $entity->setUpdatedBy($user);

        $entity->updateTimestamps();
    }
}
