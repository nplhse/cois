<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';

    public const BASE_USER_REFERENCE = 'base-user';

    public const OTHER_USER_REFERENCE = 'other-user';

    /**
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $admin = UserFactory::new(['username' => 'admin'])->asAdmin()->create();
        $user1 = UserFactory::new(['username' => 'foo'])->create();
        $user2 = UserFactory::new(['username' => 'user123'])->withNoAccess()->create()->expireCredentials();

        UserFactory::createMany(10);

        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);
        $this->addReference(self::BASE_USER_REFERENCE, $user1);
        $this->addReference(self::OTHER_USER_REFERENCE, $user2);
    }
}
