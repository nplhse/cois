<?php

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\User\ChangeUsernameCommand;
use App\Domain\Event\User\UserChangedUsernameEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ChangeUsernameHandler implements HandlerInterface
{
    private UserRepositoryInterface $userRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(UserRepositoryInterface $userRepository, EventDispatcherInterface $dispatcher)
    {
        $this->userRepository = $userRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(ChangeUsernameCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneById($command->getId());

        $user->setUsername($command->getUsername());

        $this->userRepository->save();

        $event = new UserChangedUsernameEvent($user->getId());
        $this->dispatcher->dispatch($event, UserChangedUsernameEvent::NAME);
    }
}
