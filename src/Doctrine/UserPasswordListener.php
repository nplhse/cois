<?php

namespace App\Doctrine;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as Orm;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordListener
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    #[Orm\PrePersist]
    public function prePersist(User $user, LifecycleEventArgs $args): void
    {
        $password = $this->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        $user->eraseCredentials();
    }

    #[Orm\PreUpdate]
    public function preUpdate(User $user, PreUpdateEventArgs $args): void
    {
        if ($user->getPlainPassword()) {
            $password = $this->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->eraseCredentials();
        }
    }

    private function encodePassword(User $user, string $password): string
    {
        return $this->hasher->hashPassword($user, $password);
    }
}
