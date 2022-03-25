<?php

namespace App\Tests\Application\Controller\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class RegistrationTest extends WebTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testRegistration(): void
    {
        $this->browser()
            ->visit('/register/')
            ->assertSeeIn('h1', 'Register')
            ->fillField('Username', 'newUser')
            ->fillField('Password', 'password')
            ->fillField('Repeat Password', 'password')
            ->fillField('Email', 'newUser@email.dev')
            ->click('Create account')
            ->assertOn('/dashboard/')
            ->assertSee('Logged in as: newUser')
            ->assertSeeIn('h1', 'Welcome to Collaborative IVENA statistics')
            ->assertSee('Your E-Mail is not verified')
            ->assertSee('Account has not been confirmed by admin.')
        ;
    }
}
