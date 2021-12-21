<?php

namespace App\Tests\Unit\Domain\Entity;

use _PHPStan_c862bb974\Nette\Utils\DateTime;
use App\Domain\Entity\State;
use PHPUnit\Framework\TestCase;

class StateTest extends TestCase
{
    public function testName(): void
    {
        $name = 'Test State';
        $state = new State();

        $state->setName($name);
        $this->assertEquals($name, $state->getName());
        $this->assertEquals($name, (string) $state);
    }

    public function testId(): void
    {
        $id = 123;
        $state = new State();

        $state->setId($id);
        $this->assertEquals($id, $state->getId());
    }

    public function testTimestamps(): void
    {
        $time = new DateTime('NOW');
        $state = new State();

        $this->assertInstanceOf(\DateTimeInterface::class, $state->getCreatedAt());
        $this->assertNull($state->getUpdatedAt());

        $state->setCreatedAt($time);
        $this->assertEquals($time, $state->getCreatedAt());

        $state->setUpdatedAt($time);
        $this->assertEquals($time, $state->getUpdatedAt());
    }
}
