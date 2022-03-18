<?php

namespace App\Tests\Application\Controller\Settings\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class PasswordControllerTest extends WebTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testChangePassword(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'foo']);

        $this->browser()
            ->actingAs($testUser)
            ->visit('/settings/password')
            ->assertSee('Change your Password')
            ->fillField('Current password', 'password')
            ->fillField('New password', 'test123')
            ->fillField('Repeat new password', 'test123')
            ->click('Change Password')
            ->assertSuccessful()
            ->assertSee('Your password has been changed successfully.')
            ->visit('/logout')
            ->visit('/login')
            ->fillField('Username', 'foo')
            ->fillField('Password', 'test123')
            ->click('Login')
            ->assertOn('/dashboard/')
            ->assertSee('Logged in as: foo')
        ;
    }
}
