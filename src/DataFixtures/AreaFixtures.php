<?php

namespace App\DataFixtures;

use App\Entity\DispatchArea;
use App\Entity\State;
use App\Entity\SupplyArea;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AreaFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $state1 = new State();
        $state1->setName('Test State');

        $state2 = new State();
        $state2->setName('Another Test State');

        $dispatchArea = new DispatchArea();
        $dispatchArea->setName('Test Dispatch Area');
        $dispatchArea->setState($state1);

        $state1->addDispatchArea($dispatchArea);

        $supplyArea = new SupplyArea();
        $supplyArea->setName('Test Supply Area');
        $supplyArea->setState($state2);

        $state2->addSupplyArea($supplyArea);

        $manager->persist($state1);
        $manager->persist($state2);
        $manager->persist($dispatchArea);
        $manager->persist($supplyArea);

        $manager->flush();
    }
}
