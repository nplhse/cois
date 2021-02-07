<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginLogout(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('html h1', 'Login');

        $crawler = $client->submitForm('Login', [
            'security_login[username]' => 'foo',
            'security_login[password]' => 'bar',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('html body', 'Logged in as: foo');

        $client->request('GET', '/logout');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('html body', 'Login');
    }
}
