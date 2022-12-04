<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\User\ExpireUserCommand;
use App\Domain\Event\User\UserPromotedEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;

class ExpireUserHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(ExpireUserCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['id' => $command->getId()]);

        $user->expireCredentials();

        $this->userRepository->save();

        $this->dispatchEvent(new UserPromotedEvent($user->getId(), $user->isVerified(), $user->isParticipant()));
    }
}
