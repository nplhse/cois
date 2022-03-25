<?php

namespace App\Tests\Application;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\ResetDatabase;

class UrlTest extends WebTestCase
{
    use ResetDatabase;

    /**
     * @dataProvider getPublicUrls
     */
    public function testPublicUrls(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider getSecureUrls
     */
    public function testSecureUrls(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseRedirects(
            '/login',
            Response::HTTP_FOUND
        );
    }

    public function getPublicUrls(): ?\Generator
    {
        yield ['/'];
        yield ['/login'];
        yield ['reset-password/'];
    }

    public function getSecureUrls(): ?\Generator
    {
        yield ['/dashboard/'];
        yield ['/settings'];
        yield ['/settings/profile'];
        yield ['/settings/email'];
        yield ['/settings/password'];
        yield ['/hospitals/'];
        yield ['/hospitals/new'];
        yield ['/allocations/'];
        yield ['/import/'];
    }
}
