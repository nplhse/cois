<?php

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\User\CreateUserCommand;
use App\Domain\Event\User\UserCreatedEvent;
use App\Domain\Event\User\UserRegistered;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserHandler implements HandlerInterface
{
    private UserRepositoryInterface $userRepository;

    private UserPasswordHasherInterface $passwordHasher;

    private EventDispatcherInterface $dispatcher;

    public function __construct(UserRepositoryInterface $userRepository, UserPasswordHasherInterface $passwordHasher, EventDispatcherInterface $dispatcher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;

        $this->dispatcher = $dispatcher;
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

        $event = new UserRegistered($user);
        $this->dispatcher->dispatch($event, UserCreatedEvent::NAME);
    }
}
