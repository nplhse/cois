<?php

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\User\RegisterUserCommand;
use App\Domain\Event\User\UserRegisteredEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;

class RegisterUserHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        $user = new User();
        $user->setUsername($command->getUsername());
        $user->setEmail($command->getEmail());

        $user->setPlainPassword($command->getPlainPassword());

        $this->userRepository->add($user);

        // Refresh entity from database
        $user = $this->userRepository->findOneByUsername($command->getUsername());

        $this->dispatchEvent(new UserRegisteredEvent($user));
    }
}
