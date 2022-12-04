<?php

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\User\ChangePasswordCommand;
use App\Domain\Event\User\UserChangedPasswordEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;

class ChangePasswordHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(ChangePasswordCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneById($command->getId());

        $user->setPlainPassword($command->getPlainPassword());

        $this->userRepository->save();

        $this->dispatchEvent(new UserChangedPasswordEvent($user->getId()));
    }
}
