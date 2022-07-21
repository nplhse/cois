<?php

namespace App\Tests\Application\Controller;

use App\Factory\AllocationFactory;
use App\Factory\DispatchAreaFactory;
use App\Factory\HospitalFactory;
use App\Factory\ImportFactory;
use App\Factory\StateFactory;
use App\Factory\SupplyAreaFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class WelcomeControllerTest extends WebTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testWelcome(): void
    {
        $this->browser()
            ->visit('/')
            ->assertSeeIn('h1', 'Welcome to')
        ;
    }

    public function testStatistics(): void
    {
        StateFactory::createMany(3);

        DispatchAreaFactory::createMany(8, static fn () => ['state' => StateFactory::random()]);

        SupplyAreaFactory::createMany(3, static fn () => ['state' => StateFactory::random()]);

        HospitalFactory::createMany(2, static fn () => [
            'owner' => UserFactory::random(),
            'state' => StateFactory::random(),
            'dispatchArea' => DispatchAreaFactory::random(),
            'supplyArea' => SupplyAreaFactory::random(),
        ]);

        ImportFactory::createMany(6, static fn () => [
            'user' => UserFactory::random(),
            'hospital' => HospitalFactory::random(),
        ]);

        AllocationFactory::createMany(7, static fn () => [
            'hospital' => HospitalFactory::random(),
            'import' => ImportFactory::random(),
            'state' => StateFactory::random(),
            'dispatchArea' => DispatchAreaFactory::random(),
            'supplyArea' => SupplyAreaFactory::random(),
        ]);

        $this->browser()
            ->visit('/')
            ->assertContains('4')
            ->assertContains('2')
            ->assertContains('7')
            ->assertContains('6')
        ;
    }
}
