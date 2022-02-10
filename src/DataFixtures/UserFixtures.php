<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    /**
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $admin = UserFactory::new(['username' => 'admin'])->asAdmin()->create();
        $user1 = UserFactory::new(['username' => 'foo'])->create();
        $user2 = UserFactory::new(['username' => 'randomUser'])->withNoAccess()->create()->expireCredentials();

        UserFactory::createMany(10);

        $manager->flush();
    }
}
