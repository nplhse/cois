<?php

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\User\PromoteUserCommand;
use App\Domain\Event\User\UserPromotedEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;

class PromoteUserHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(PromoteUserCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneById($command->getId());

        if ($command->getIsVerified()) {
            $user->verify();
        } else {
            $user->unverify();
        }

        if ($command->getIsParticipant()) {
            $user->enableParticipation();
        } else {
            $user->disableParticipation();
        }

        $this->userRepository->save();

        $this->dispatchEvent(new UserPromotedEvent($user->getId(), $user->isVerified(), $user->isParticipant()));
    }
}
