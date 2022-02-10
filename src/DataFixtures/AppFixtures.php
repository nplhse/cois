<?php

namespace App\DataFixtures;

use App\Factory\AllocationFactory;
use App\Factory\DispatchAreaFactory;
use App\Factory\HospitalFactory;
use App\Factory\ImportFactory;
use App\Factory\StateFactory;
use App\Factory\SupplyAreaFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        StateFactory::createMany(3);

        DispatchAreaFactory::createMany(8, static fn() => ['state' => StateFactory::random()]);

        SupplyAreaFactory::createMany(3, static fn() => ['state' => StateFactory::random()]);

        HospitalFactory::createMany(random_int(3, 10), static fn() => [
            'owner' => UserFactory::random(),
            'state' => StateFactory::random(),
            'dispatchArea' => DispatchAreaFactory::random(),
            'supplyArea' => SupplyAreaFactory::random(),
        ]);

        ImportFactory::createMany(random_int(3, 5), static fn() => [
            'user' => UserFactory::random(),
            'hospital' => HospitalFactory::random(),
        ]);

        AllocationFactory::createMany(random_int(128, 256), static fn() => [
            'hospital' => HospitalFactory::random(),
            'import' => ImportFactory::random(),
            'dispatchArea' => DispatchAreaFactory::random()->getName(),
            'supplyArea' => SupplyAreaFactory::random()->getName(),
        ]);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    private function array_random(array $arr, int $num = 1): mixed
    {
        shuffle($arr);

        $r = [];
        for ($i = 0; $i < $num; ++$i) {
            $r[] = $arr[$i];
        }

        return 1 == $num ? $r[0] : $r;
    }
}
