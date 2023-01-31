<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\User\ChangeProfileCommand;
use App\Domain\Event\User\UserChangedProfileEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;

class ChangeProfileHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(ChangeProfileCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneById($command->getId());

        $user->setFullName($command->getFullName());
        $user->setBiography($command->getBiography());
        $user->setLocation($command->getLocation());
        $user->setWebsite($command->getWebsite());

        $this->userRepository->save();

        $this->dispatchEvent(new UserChangedProfileEvent($user->getId()));
    }
}
