<?php

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\User\PromoteUserCommand;
use App\Domain\Event\User\UserPromotedEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PromoteUserHandler implements HandlerInterface
{
    private UserRepositoryInterface $userRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(UserRepositoryInterface $userRepository, EventDispatcherInterface $dispatcher)
    {
        $this->userRepository = $userRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(PromoteUserCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneById($command->getId());

        if ($command->getIsVerified()) {
            $user->verify();
        } else {
            $user->unverify();
        }

        if ($command->getIsParticipant()) {
            $user->enableParticipation();
        } else {
            $user->disableParticipation();
        }

        $this->userRepository->save();

        $event = new UserPromotedEvent($user->getId(), $user->isVerified(), $user->isParticipant());
        $this->dispatcher->dispatch($event, UserPromotedEvent::NAME);
    }
}
