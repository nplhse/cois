<?php

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\User\EditUserCommand;
use App\Domain\Event\User\UserEditedEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EditUserHandler implements HandlerInterface
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

        dump($user);

        $this->userRepository->add($user);

        // Refresh entity from database
        $user = $this->userRepository->findOneByUsername($command->getUsername());

        $event = new UserEditedEvent($user);
        $this->dispatcher->dispatch($event, UserEditedEvent::NAME);
    }
}
