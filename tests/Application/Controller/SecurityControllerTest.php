<?php

namespace App\Tests\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class SecurityControllerTest extends WebTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testLoginLogout(): void
    {
        $this->browser()
            ->visit('/login')
            ->dump()
            ->assertSeeIn('h1', 'Login')
            ->fillField('Username', 'foo')
            ->fillField('Password', 'password')
            ->click('Login')
            ->assertOn('/dashboard/')
            ->assertSee('Logged in as: foo')
            ->assertNotSee('Login')
            ->visit('/logout')
            ->assertNotSee('Logged in as: foo')
        ;
    }
}
