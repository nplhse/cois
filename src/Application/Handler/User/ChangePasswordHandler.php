<?php

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\User\ChangePasswordCommand;
use App\Domain\Event\User\UserChangedPasswordEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordHandler implements HandlerInterface
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

    public function __invoke(ChangePasswordCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneById($command->getId());

        $user->setPlainPassword($command->getPlainPassword());
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
        $user->eraseCredentials();

        $this->userRepository->save();

        $event = new UserChangedPasswordEvent($user->getId());
        $this->dispatcher->dispatch($event, UserChangedPasswordEvent::NAME);
    }
}
