<?php

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

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
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
