<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';

    public const BASE_USER_REFERENCE = 'base-user';

    public const OTHER_USER_REFERENCE = 'other-user';

    private \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setEmail('admin@bar.dev');
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsCredentialsExpired(false);
        $admin->setIsParticipant(true);
        $admin->setIsVerified(true);

        $manager->persist($admin);

        $user1 = new User();
        $user1->setEmail('foo@bar.dev');
        $user1->setUsername('foo');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'foobar'));
        $user1->setIsCredentialsExpired(false);
        $user1->setIsParticipant(true);
        $user1->setIsVerified(true);

        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('foo@bar.dev');
        $user2->setUsername('user123');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'foobar'));
        $user2->setIsCredentialsExpired(false);
        $user2->setIsParticipant(true);
        $user2->setIsVerified(true);

        $manager->persist($user2);

        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);
        $this->addReference(self::BASE_USER_REFERENCE, $user1);
        $this->addReference(self::OTHER_USER_REFERENCE, $user2);
    }
}
