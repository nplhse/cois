<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller\Settings\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class EmailControllerTest extends WebTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testChangeEmail(): void
    {
        $this->markTestSkipped('Need to redo this part.');
    }
}
