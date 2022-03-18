<?php

namespace App\Tests\Application\Controller\Settings;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class SettingsControllerTest extends WebTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testDashboard(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'foo']);

        $this->browser()
            ->actingAs($testUser)
            ->visit('/settings')
            ->assertSee('Account')
            ->assertNotSee('Administration')
        ;
    }
}
