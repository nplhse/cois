<?php

declare(strict_types=1);

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\User\CreateUserCommand;
use App\Domain\Event\User\UserRegisteredEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $user = new User();
        $user->setUsername($command->getUsername());
        $user->setEmail($command->getEmail());

        foreach ($command->getRoles() as $role) {
            $user->addRole($role);
        }

        $user->setPlainPassword($command->getPlainPassword());
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
        $user->eraseCredentials();

        if ($command->hasCredentialsExpired()) {
            $user->expireCredentials();
        }

        if ($command->isVerified()) {
            $user->verify();
        } else {
            $user->unverify();
        }

        if ($command->isParticipant()) {
            $user->enableParticipation();
        } else {
            $user->disableParticipation();
        }

        $this->userRepository->add($user);

        // Refresh entity from database
        $user = $this->userRepository->findOneByUsername($command->getUsername());

        $this->dispatchEvent(new UserRegisteredEvent($user));
    }
}
