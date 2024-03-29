<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Entity\User;
use Domain\Command\User\RegisterUserCommand;
use Domain\Event\User\UserRegisteredEvent;
use Domain\Repository\UserRepositoryInterface;

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
