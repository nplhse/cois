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

    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

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

        $user = new User();
        $user->setEmail('foo@bar.dev');
        $user->setUsername('foo');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'foobar'));
        $user->setIsCredentialsExpired(false);
        $user->setIsParticipant(true);
        $user->setIsVerified(true);

        $manager->persist($user);
        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);
        $this->addReference(self::BASE_USER_REFERENCE, $user);
    }
}
