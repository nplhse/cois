<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller\Settings\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class EmailControllerTest extends WebTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testChangeEmail(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'foo']);

        $this->browser()
            ->actingAs($testUser)
            ->visit('/settings/email')
            ->assertSee('E-Mail settings')
            ->assertSee('Your E-Mail address is currently: verified.')
            ->fillField('New email', 'testEmail@example.com')
            ->click('Update E-Mail settings')
            ->assertSuccessful()
            ->assertSee('Your current E-Mail address is: testEmail@example.com')
            ->assertSee('Your E-Mail address is currently: not verified.')
        ;
    }
}
