<?php

namespace App\Application\Handler\User;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\User\ResetCredentialsCommand;
use App\Domain\Event\User\UserResetCredentialsEvent;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetCredentialsHandler implements HandlerInterface
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

    public function __invoke(ResetCredentialsCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneById($command->getId());

        $user->setPlainPassword($command->getPlainPassword());
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
        $user->eraseCredentials();

        $this->userRepository->save();

        $event = new UserResetCredentialsEvent($user->getId());
        $this->dispatcher->dispatch($event, UserResetCredentialsEvent::NAME);
    }
}
