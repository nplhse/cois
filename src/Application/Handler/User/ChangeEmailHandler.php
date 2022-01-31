<?php

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\User\ChangeEmailCommand;
use App\Domain\Event\User\UserChangedEmailEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ChangeEmailHandler implements HandlerInterface
{
    private UserRepositoryInterface $userRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(UserRepositoryInterface $userRepository, EventDispatcherInterface $dispatcher)
    {
        $this->userRepository = $userRepository;
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(ChangeEmailCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneById($command->getId());

        $user->setEmail($command->getEmail());
        $user->unverify();

        $this->userRepository->save();

        $event = new UserChangedEmailEvent($user);
        $this->dispatcher->dispatch($event, UserChangedEmailEvent::NAME);
    }
}
