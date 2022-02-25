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
        UserFactory::new(['username' => 'admin'])->asAdmin()->create()->verify()->enableParticipation();

        UserFactory::new(['username' => 'foo'])->create()->verify()->enableParticipation();

        UserFactory::new(['username' => 'unknownUser'])->create();

        UserFactory::new(['username' => 'expiredUser'])->create()->verify()->enableParticipation()->expireCredentials();

        UserFactory::createMany(random_int(3, 10));

        $manager->flush();
    }
}
