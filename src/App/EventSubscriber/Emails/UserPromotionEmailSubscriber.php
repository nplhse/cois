<?php

declare(strict_types=1);

namespace App\EventSubscriber\Emails;

use App\Domain\Event\User\UserPromotedEvent;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Mailers\UserPromotedMailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserPromotionEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPromotedMailerService $mailerService,
    ) {
    }

    public function onUserPromotedEvent(UserPromotedEvent $event): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneById($event->getId());

        if (null !== $user && $event->getIsParticipant()) {
            $this->mailerService->sendUserPromotedEmail($user);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserPromotedEvent::NAME => 'onUserPromotedEvent',
        ];
    }
}
