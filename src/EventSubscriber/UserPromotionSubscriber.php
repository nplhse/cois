<?php

namespace App\EventSubscriber;

use App\Domain\Event\User\UserPromotedEvent;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserPromotionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private MailerService $mailerService,
    ) {
    }

    public function onUserPromotedEvent(UserPromotedEvent $event): void
    {
        $user = $this->userRepository->findOneById($event->getId());

        if (null !== $user && $event->getIsParticipant()) {
            $this->mailerService->sendPromotionEmail($user);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserPromotedEvent::NAME => 'onUserPromotedEvent',
        ];
    }
}
