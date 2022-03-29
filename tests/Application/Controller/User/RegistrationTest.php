<?php

namespace App\Tests\Application\Controller\User;

use App\Domain\Enum\Page\PageStatusEnum;
use App\Domain\Enum\Page\PageTypeEnum;
use App\Factory\PageFactory;
use App\Factory\SettingFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class RegistrationTest extends WebTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testRegistration(): void
    {
        SettingFactory::createOne([
            'name' => 'enable_registration',
            'value' => 'true',
            'type' => 'boolean',
            'category' => 'user',
        ]);

        SettingFactory::createOne([
            'name' => 'enable_terms',
            'value' => 'true',
            'type' => 'boolean',
            'category' => 'user',
        ]);

        PageFactory::createOne([
            'title' => 'Privacy',
            'slug' => 'privacy',
            'type' => PageTypeEnum::PrivacyPage,
            'status' => PageStatusEnum::Published,
            'createdBy' => UserFactory::random(),
        ]);

        PageFactory::createOne([
            'title' => 'Terms and conditions',
            'slug' => 'terms',
            'type' => PageTypeEnum::TermsPage,
            'status' => PageStatusEnum::Published,
            'createdBy' => UserFactory::random(),
        ]);

        $this->browser()
            ->visit('/register')
            ->assertSeeIn('h1', 'Register')
            ->fillField('Username', 'newUser')
            ->fillField('Password', 'password')
            ->fillField('Repeat Password', 'password')
            ->checkField('registration_form_agreeTerms')
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
