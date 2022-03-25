<?php

namespace App\Tests\Story;

use App\Factory\SettingFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class GlobalStory extends Story
{
    public function build(): void
    {
        UserFactory::new(['username' => 'admin'])->asAdmin()->create()->verify()->enableParticipation();
        UserFactory::new(['username' => 'foo'])->create()->verify()->enableParticipation();
        UserFactory::new(['username' => 'unknownUser'])->create();
        UserFactory::new(['username' => 'expiredUser'])->create()->verify()->enableParticipation()->expireCredentials();

        SettingFactory::new(['name' => 'title', 'value' => 'Collaborative IVENA statistics', 'type' => 'string', 'category' => 'general']);
        SettingFactory::new(['name' => 'enable_registration', 'value' => 'true', 'type' => 'boolean', 'category' => 'user']);
        SettingFactory::new(['name' => 'enable_cookie_consent', 'value' => 'true', 'type' => 'boolean', 'category' => 'user']);
    }
}
