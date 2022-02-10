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
        UserFactory::new(['username' => 'admin'])->asAdmin()->create();

        UserFactory::new(['username' => 'foo'])->create();

        UserFactory::new(['username' => 'randomUser'])->withNoAccess()->create();

        UserFactory::new(['username' => 'expiredUser'])->withNoAccess()->create()->expireCredentials();

        UserFactory::createMany(10);

        $manager->flush();
    }
}
