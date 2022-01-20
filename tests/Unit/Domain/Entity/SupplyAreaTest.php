<?php

namespace App\Tests\Unit\Domain\Entity;

use _PHPStan_c862bb974\Nette\Utils\DateTime;
use App\Domain\Entity\SupplyArea;
use PHPUnit\Framework\TestCase;

class SupplyAreaTest extends TestCase
{
    public function testName(): void
    {
        $name = 'Test Area';
        $supplyArea = new SupplyArea();

        $supplyArea->setName($name);
        $this->assertEquals($name, $supplyArea->getName());
        $this->assertEquals($name, (string) $supplyArea);
    }

    public function testId(): void
    {
        $id = 123;
        $supplyArea = new SupplyArea();

        $supplyArea->setId($id);
        $this->assertEquals($id, $supplyArea->getId());
    }

    public function testTimestamps(): void
    {
        $time = new \DateTime('NOW');
        $supplyArea = new SupplyArea();

        $this->assertInstanceOf(\DateTimeInterface::class, $supplyArea->getCreatedAt());
        $this->assertNull($supplyArea->getUpdatedAt());

        $supplyArea->setCreatedAt($time);
        $this->assertEquals($time, $supplyArea->getCreatedAt());

        $supplyArea->setUpdatedAt($time);
        $this->assertEquals($time, $supplyArea->getUpdatedAt());
    }
}
