<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\User\ChangeEmailCommand;
use App\Domain\Event\User\UserChangedEmailEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;

class ChangeEmailHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(ChangeEmailCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneById($command->getId());

        $user->setEmail($command->getEmail());
        $user->unverify();

        $this->userRepository->save();

        $this->dispatchEvent(new UserChangedEmailEvent($user));
    }
}
