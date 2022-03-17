<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\ImportInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Entity\Allocation;
use PHPUnit\Framework\TestCase;

class AllocationTest extends TestCase
{
    public function testHospital(): void
    {
        $hospitalName = 'Test Hospital';
        $allocation = new Allocation();

        $hospital = $this->createMock(HospitalInterface::class);
        $hospital->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($hospitalName);

        $allocation->setHospital($hospital);
        $this->assertEquals($hospitalName, $allocation->getHospital()->getName());
    }

    public function testImport(): void
    {
        $importName = 'Test Import';
        $allocation = new Allocation();

        $import = $this->createMock(ImportInterface::class);
        $import->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($importName);

        $allocation->setImport($import);
        $this->assertEquals($importName, $allocation->getImport()->getName());
    }

    public function testState(): void
    {
        $stateName = 'Test State';
        $allocation = new Allocation();

        $state = $this->createMock(StateInterface::class);
        $state->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($stateName);

        $allocation->setState($state);
        $this->assertEquals($stateName, $allocation->getState()->getName());
    }

    public function testDispatchArea(): void
    {
        $areaName = 'Test Area';
        $allocation = new Allocation();

        $dispatchArea = $this->createMock(DispatchAreaInterface::class);
        $dispatchArea->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($areaName);

        $allocation->setDispatchArea($dispatchArea);
        $this->assertEquals($areaName, $allocation->getDispatchArea()->getName());
    }

    public function testSupplyArea(): void
    {
        $areaName = 'Test Area';
        $allocation = new Allocation();

        $supplyArea = $this->createMock(SupplyAreaInterface::class);
        $supplyArea->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($areaName);

        $allocation->setSupplyArea($supplyArea);
        $this->assertEquals($areaName, $allocation->getSupplyArea()->getName());
    }

    public function testId(): void
    {
        $id = 123;
        $allocation = new Allocation();

        $allocation->setId($id);
        $this->assertEquals($id, $allocation->getId());
    }

    public function testTimestamps(): void
    {
        $time = new \DateTime();
        $time->setTimestamp(mktime(12, 15, 00, 1, 1, 2020));
        $allocation = new Allocation();

        $allocation->setCreatedAt($time);
        $this->assertEquals($time, $allocation->getCreatedAt());
        $this->assertEquals('01.01.2020', $allocation->getCreationDate());
        $this->assertEquals('2020', $allocation->getCreationYear());
        $this->assertEquals('1', $allocation->getCreationMonth());
        $this->assertEquals('1', $allocation->getCreationDay());
        $this->assertEquals('3', $allocation->getCreationWeekday());
        $this->assertEquals('12:15', $allocation->getCreationTime());
        $this->assertEquals('12', $allocation->getCreationHour());
        $this->assertEquals('15', $allocation->getCreationMinute());

        $allocation->setArrivalAt($time);
        $this->assertEquals($time, $allocation->getArrivalAt());
        $this->assertEquals('01.01.2020', $allocation->getArrivalDate());
        $this->assertEquals('2020', $allocation->getArrivalYear());
        $this->assertEquals('1', $allocation->getArrivalMonth());
        $this->assertEquals('1', $allocation->getArrivalDay());
        $this->assertEquals('3', $allocation->getArrivalWeekday());
        $this->assertEquals('12:15', $allocation->getArrivalTime());
        $this->assertEquals('12', $allocation->getArrivalHour());
        $this->assertEquals('15', $allocation->getArrivalMinute());
    }

    public function testProperties(): void
    {
        $allocation = new Allocation();

        $allocation->setOccasion('Occasion');
        $this->assertEquals('Occasion', $allocation->getOccasion());

        $allocation->setAssignment('Assignment');
        $this->assertEquals('Assignment', $allocation->getAssignment());

        $allocation->setRequiresResus(true);
        $this->assertTrue($allocation->getRequiresResus());

        $allocation->setRequiresCathlab(true);
        $this->assertTrue($allocation->getRequiresCathlab());

        $allocation->setIsCPR(true);
        $this->assertTrue($allocation->getIsCPR());

        $allocation->setIsVentilated(true);
        $this->assertTrue($allocation->getIsVentilated());

        $allocation->setIsShock(true);
        $this->assertTrue($allocation->getIsShock());

        $allocation->setIsInfectious('Keine');
        $this->assertEquals('Keine', $allocation->getIsInfectious());

        $allocation->setIsPregnant(true);
        $this->assertTrue($allocation->getIsPregnant());

        $allocation->setIsWorkAccident(true);
        $this->assertTrue($allocation->getIsWorkAccident());

        $allocation->setIsWithPhysician(true);
        $this->assertTrue($allocation->getIsWithPhysician());

        $allocation->setModeOfTransport('Boden');
        $this->assertEquals('Boden', $allocation->getModeOfTransport());

        $allocation->setSpeciality('Speciality');
        $this->assertEquals('Speciality', $allocation->getSpeciality());

        $allocation->setSpecialityDetail('SpecialityDetail');
        $this->assertEquals('SpecialityDetail', $allocation->getSpecialityDetail());

        $allocation->setSpecialityWasClosed(true);
        $this->assertEquals(true, $allocation->getSpecialityWasClosed());

        $allocation->setHandoverPoint('Handover Point');
        $this->assertEquals('Handover Point', $allocation->getHandoverPoint());

        $allocation->setComment('Comment');
        $this->assertEquals('Comment', $allocation->getComment());
    }

    public function testUrgency(): void
    {
        $allocation = new Allocation();

        $allocation->setUrgency(1);
        $this->assertEquals(1, $allocation->getUrgency());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Urgency must be between 1 and 3, not -1');
        $allocation->setUrgency(-1);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Urgency must be between 1 and 3, not -1');
        $allocation->setUrgency(10);
    }

    public function testGender(): void
    {
        $allocation = new Allocation();

        $allocation->setGender('M');
        $this->assertEquals('M', $allocation->getGender());

        $allocation->setGender('W');
        $this->assertEquals('W', $allocation->getGender());

        $allocation->setGender('D');
        $this->assertEquals('D', $allocation->getGender());

        try {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage('Empty Gender is not a valid option');
            $allocation->setGender((string) null);
        } catch (\InvalidArgumentException $e) {
            // Continue
        }

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Gender X is not a valid option');
        $allocation->setGender('X');
    }

    public function testAge(): void
    {
        $allocation = new Allocation();

        $allocation->setAge(0);
        $this->assertEquals(0, $allocation->getAge());

        $allocation->setAge(10);
        $this->assertEquals(10, $allocation->getAge());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Age must not be below 0');
        $allocation->setAge(-10);
    }

    public function testIndication(): void
    {
        $allocation = new Allocation();

        $allocation->setIndication('Indication');
        $this->assertEquals('Indication', $allocation->getIndication());

        $allocation->setIndicationCode(123);
        $this->assertEquals(123, $allocation->getIndicationCode());

        $this->assertNull($allocation->getSecondaryIndication());
        $this->assertNull($allocation->getSecondaryIndicationCode());

        $allocation->setSecondaryIndication('Indication');
        $this->assertEquals('Indication', $allocation->getSecondaryIndication());

        $allocation->setSecondaryIndicationCode(123);
        $this->assertEquals(123, $allocation->getSecondaryIndicationCode());
    }
}
