<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures manages fixtures for the User entity.
 */
class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Create and store User fixtures in the database.
     */
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $data) {
            $user = new User();
            $user->setUsername($data['username']);
            $user->setPassword($this->encoder->encodePassword($user, $data['password']));
            $user->setEmail($data['email']);
            $user->setRoles($data['roles']);

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * Returns data for the default User objects we want to create.
     *
     * @return array[]
     */
    public function getData(): array
    {
        return
            [
                [
                    'username' => 'admin',
                    'password' => 'admin',
                    'email' => 'admin@admin.test',
                    'roles' => ['ROLE_ADMIN'],
                ],
                [
                    'username' => 'foo',
                    'password' => 'bar',
                    'email' => 'foo@bar.com',
                    'roles' => ['ROLE_USER'],
                ],
            ];
    }
}
