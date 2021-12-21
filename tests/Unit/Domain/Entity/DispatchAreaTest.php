<?php

namespace App\Tests\Unit\Domain\Entity;

use _PHPStan_c862bb974\Nette\Utils\DateTime;
use App\Domain\Contracts\StateInterface;
use App\Domain\Entity\DispatchArea;
use PHPUnit\Framework\TestCase;

class DispatchAreaTest extends TestCase
{
    public function testName(): void
    {
        $name = 'Test Area';
        $dispatchArea = new DispatchArea();

        $dispatchArea->setName($name);
        $this->assertEquals($name, $dispatchArea->getName());
        $this->assertEquals($name, (string) $dispatchArea);
    }

    public function testState(): void
    {
        $stateName = 'Test State';

        $state = $this->createMock(StateInterface::class);
        $state->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($stateName);

        $dispatchArea = new DispatchArea();

        $dispatchArea->setState($state);
        $this->assertEquals($stateName, $dispatchArea->getState()->getName());
    }

    public function testId(): void
    {
        $id = 123;
        $dispatchArea = new DispatchArea();

        $dispatchArea->setId($id);
        $this->assertEquals($id, $dispatchArea->getId());
    }

    public function testTimestamps(): void
    {
        $time = new DateTime('NOW');
        $dispatchArea = new DispatchArea();

        $this->assertInstanceOf(\DateTimeInterface::class, $dispatchArea->getCreatedAt());
        $this->assertNull($dispatchArea->getUpdatedAt());

        $dispatchArea->setCreatedAt($time);
        $this->assertEquals($time, $dispatchArea->getCreatedAt());

        $dispatchArea->setUpdatedAt($time);
        $this->assertEquals($time, $dispatchArea->getUpdatedAt());
    }
}
