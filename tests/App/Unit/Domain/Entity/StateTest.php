<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Entity\Hospital;
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
        $time = new \DateTime('NOW');
        $state = new State();

        $this->assertInstanceOf(\DateTimeInterface::class, $state->getCreatedAt());
        $this->assertNull($state->getUpdatedAt());

        $state->setCreatedAt($time);
        $this->assertEquals($time, $state->getCreatedAt());

        $state->setUpdatedAt($time);
        $this->assertEquals($time, $state->getUpdatedAt());
    }

    public function testDispatchAreas(): void
    {
        $areaName1 = 'Test Area';
        $areaName2 = 'Demo Area';
        $state = new State();

        $area1 = $this->createMock(DispatchAreaInterface::class);
        $area1->expects($this->exactly(2))
            ->method('getName')
            ->willReturn($areaName1);

        $area2 = $this->createMock(DispatchAreaInterface::class);
        $area2->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($areaName2);

        $state->addDispatchArea($area1);
        $this->assertEquals($areaName1, $state->getDispatchAreas()->first()->getName());

        $state->addDispatchArea($area2);
        $this->assertEquals($areaName2, $state->getDispatchAreas()->last()->getName());

        $state->removeDispatchArea($area2);
        $this->assertEquals($areaName1, $state->getDispatchAreas()->last()->getName());
    }

    public function testSupplyAreas(): void
    {
        $areaName1 = 'Test Area';
        $areaName2 = 'Demo Area';
        $state = new State();

        $area1 = $this->createMock(SupplyAreaInterface::class);
        $area1->expects($this->exactly(2))
            ->method('getName')
            ->willReturn($areaName1);

        $area2 = $this->createMock(SupplyAreaInterface::class);
        $area2->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($areaName2);

        $state->addSupplyArea($area1);
        $this->assertEquals($areaName1, $state->getSupplyAreas()->first()->getName());

        $state->addSupplyArea($area2);
        $this->assertEquals($areaName2, $state->getSupplyAreas()->last()->getName());

        $state->removeSupplyArea($area2);
        $this->assertEquals($areaName1, $state->getSupplyAreas()->last()->getName());
    }

    public function testHospitals(): void
    {
        $hospitalName = 'Test Hospital';

        $hospital = $this->createMock(Hospital::class);
        $hospital->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($hospitalName);

        $state = new State();

        $state->addHospital($hospital);
        $this->assertEquals($hospitalName, $state->getHospitals()->first()->getName());
        $this->assertCount(1, $state->getHospitals());

        $state->removeHospital($hospital);
        $this->assertCount(0, $state->getHospitals());
    }
}
