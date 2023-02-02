<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller\Settings\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProfileControllerTest extends WebTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testChangeUsername(): void
    {
        $this->markTestSkipped();

        /*
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'foo']);

        $this->browser()
            ->actingAs($testUser)
            ->visit('/settings/profile')
            ->assertSee('Change your Profile')
            ->fillField('Username', 'foobar')
            ->click('Update profile')
            ->assertSuccessful()
            ->assertSee('Your profile has been updated.')
            ->assertSee('Logged in as: foobar')
        ;
         */
    }
}
