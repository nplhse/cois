<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Entity\User;
use Domain\Command\User\ChangePasswordCommand;
use Domain\Event\User\UserChangedPasswordEvent;
use Domain\Repository\UserRepositoryInterface;

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
