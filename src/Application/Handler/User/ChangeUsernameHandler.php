<?php

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\User\ChangeUsernameCommand;
use App\Domain\Event\User\UserChangedUsernameEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;

class ChangeUsernameHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(ChangeUsernameCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneById($command->getId());

        $user->setUsername($command->getUsername());

        $this->userRepository->save();

        $this->dispatchEvent(new UserChangedUsernameEvent($user->getId()));
    }
}
