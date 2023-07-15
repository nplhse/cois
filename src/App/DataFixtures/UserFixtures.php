<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new(['username' => 'admin'])->asAdmin()->create()->verify()->enableParticipation();

        UserFactory::new(['username' => 'foo'])->create()->verify()->enableParticipation();

        UserFactory::new(['username' => 'unknownUser'])->create();

        UserFactory::new(['username' => 'expiredUser'])->create()->verify()->enableParticipation()->expireCredentials();

        UserFactory::createMany(random_int(3, 10));

        $manager->flush();
    }
}
