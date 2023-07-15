<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Entity\User;
use Domain\Command\User\EditUserCommand;
use Domain\Event\User\UserEditedEvent;
use Domain\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EditUserHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(EditUserCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneById($command->getId());

        $user->setUsername($command->getUsername());
        $user->setEmail($command->getEmail());

        foreach ($command->getRoles() as $role) {
            $user->addRole($role);
        }

        if ($user->getPlainPassword()) {
            $user->setPlainPassword($command->getPlainPassword());
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
        }

        $this->userRepository->add($user);

        // Refresh entity from database
        $user = $this->userRepository->findOneByUsername($command->getUsername());

        $this->dispatchEvent(new UserEditedEvent($user));
    }
}
