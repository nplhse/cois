<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Contracts\UserInterface;
use App\Domain\Entity\Hospital;
use App\Domain\Entity\Import;
use App\Domain\Entity\User;
use App\Domain\Enum\HospitalSize;
use PHPUnit\Framework\TestCase;

class HospitalTest extends TestCase
{
    public function testName(): void
    {
        $name = 'Test Hospital';
        $hospital = new Hospital();

        $hospital->setName($name);
        $this->assertEquals($name, $hospital->getName());
        $this->assertEquals($name, (string) $hospital);
    }

    public function testOwner(): void
    {
        $username = 'username';
        $hospital = new Hospital();

        $user = $this->createMock(UserInterface::class);
        $user->expects($this->exactly(1))
            ->method('getUsername')
            ->willReturn($username);

        $hospital->setOwner($user);
        $this->assertEquals($username, $hospital->getOwner()->getUsername());
    }

    public function testAssociatedUsers(): void
    {
        $username1 = 'foo';
        $username2 = 'bar';
        $hospital = new Hospital();

        $user1 = $this->createMock(UserInterface::class);
        $user1->expects($this->exactly(2))
            ->method('getUsername')
            ->willReturn($username1);

        $user2 = $this->createMock(UserInterface::class);
        $user2->expects($this->exactly(1))
            ->method('getUsername')
            ->willReturn($username2);

        $hospital->addAssociatedUser($user1);
        $this->assertEquals($username1, $hospital->getAssociatedUsers()->first()->getUsername());

        $hospital->addAssociatedUser($user2);
        $this->assertEquals($username2, $hospital->getAssociatedUsers()->last()->getUsername());

        $hospital->removeAssociatedUser($user2);
        $this->assertEquals($username1, $hospital->getAssociatedUsers()->last()->getUsername());
    }

    public function testAddress(): void
    {
        $address = '42 Wallaby Way, Sydney';
        $hospital = new Hospital();

        $hospital->setAddress($address);
        $this->assertEquals($address, $hospital->getAddress());
    }

    public function testState(): void
    {
        $stateName = 'Test State';
        $hospital = new Hospital();

        $state = $this->createMock(StateInterface::class);
        $state->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($stateName);

        $hospital->setState($state);
        $this->assertEquals($stateName, $hospital->getState()->getName());
    }

    public function testDispatchArea(): void
    {
        $areaName = 'Test Area';
        $hospital = new Hospital();

        $dispatchArea = $this->createMock(DispatchAreaInterface::class);
        $dispatchArea->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($areaName);

        $hospital->setDispatchArea($dispatchArea);
        $this->assertEquals($areaName, $hospital->getDispatchArea()->getName());
    }

    public function testSupplyArea(): void
    {
        $areaName = 'Test Area';
        $hospital = new Hospital();

        $supplyArea = $this->createMock(SupplyAreaInterface::class);
        $supplyArea->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($areaName);

        $hospital->setSupplyArea($supplyArea);
        $this->assertEquals($areaName, $hospital->getSupplyArea()->getName());
    }

    public function testBeds(): void
    {
        $valid = Hospital::BEDS_SMALL_HOSPITAL;
        $invalid = 0;

        $hospital = new Hospital();

        $hospital->setBeds($valid);
        $this->assertEquals($valid, $hospital->getBeds());

        $hospital->setBeds(Hospital::BEDS_SMALL_HOSPITAL - 10);
        $this->assertEquals(HospitalSize::SMALL, $hospital->getSize());

        $hospital->setBeds(Hospital::BEDS_SMALL_HOSPITAL);
        $this->assertEquals(HospitalSize::SMALL, $hospital->getSize());

        $hospital->setBeds(Hospital::BEDS_SMALL_HOSPITAL + 10);
        $this->assertEquals(HospitalSize::MEDIUM, $hospital->getSize());

        $hospital->setBeds(Hospital::BEDS_LARGE_HOSPITAL);
        $this->assertEquals(HospitalSize::LARGE, $hospital->getSize());

        $hospital->setBeds(Hospital::BEDS_LARGE_HOSPITAL + 10);
        $this->assertEquals(HospitalSize::LARGE, $hospital->getSize());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Beds must be positive integer, not 0');
        $hospital->setBeds($invalid);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Beds must be positive integer, not -10');
        $hospital->setBeds($invalid - 10);
    }

    public function testId(): void
    {
        $id = 123;
        $user = new User();

        $user->setId($id);
        $this->assertEquals($id, $user->getId());
    }

    public function testTimestamps(): void
    {
        $time = new \DateTime('NOW');
        $user = new User();

        $this->assertInstanceOf(\DateTimeInterface::class, $user->getCreatedAt());
        $this->assertNull($user->getUpdatedAt());

        $user->setCreatedAt($time);
        $this->assertEquals($time, $user->getCreatedAt());

        $user->setUpdatedAt($time);
        $this->assertEquals($time, $user->getUpdatedAt());
    }

    public function testImports(): void
    {
        $importName = 'Test Hospital';

        $import = $this->createMock(Import::class);
        $import->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($importName);

        $hospital = new Hospital();

        $hospital->addImport($import);
        $this->assertEquals($importName, $hospital->getImports()->first()->getName());
        $this->assertCount(1, $hospital->getImports());

        $hospital->removeImport($import);
        $this->assertCount(0, $hospital->getImports());
    }
}
