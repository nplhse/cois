<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Entity\User;
use Domain\Command\User\ChangeUsernameCommand;
use Domain\Event\User\UserChangedUsernameEvent;
use Domain\Repository\UserRepositoryInterface;

class ChangeUsernameHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
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
