<?php

namespace App\Tests\Story;

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
    }
}
