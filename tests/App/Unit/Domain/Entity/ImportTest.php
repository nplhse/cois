<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use Domain\Contracts\HospitalInterface;
use Domain\Contracts\UserInterface;
use Domain\Entity\Import;
use PHPUnit\Framework\TestCase;

class ImportTest extends TestCase
{
    public function testName(): void
    {
        $name = 'Test Import';
        $import = new Import();

        $import->setName($name);
        $this->assertEquals($name, $import->getName());
        $this->assertEquals($name, (string) $import);
    }

    public function testType(): void
    {
        $valid = Import::STATUS_SUCCESS;
        $invalid = 'invalidStatus';

        $import = new Import();

        $import->setStatus($valid);
        $this->assertEquals($valid, $import->getStatus());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Status invalidStatus is not a valid option');
        $import->setStatus($invalid);
    }

    public function testStatus(): void
    {
        $valid = Import::TYPE_ALLOCATION;
        $invalid = 'invalidType';

        $import = new Import();

        $import->setType($valid);
        $this->assertEquals($valid, $import->getType());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Type invalidType is not a valid option');
        $import->setType($invalid);
    }

    public function testUser(): void
    {
        $username = 'username';
        $import = new Import();

        $user = $this->createMock(UserInterface::class);
        $user->expects($this->exactly(1))
            ->method('getUsername')
            ->willReturn($username);

        $import->setUser($user);
        $this->assertEquals($username, $import->getUser()->getUsername());
    }

    public function testHospital(): void
    {
        $hospitalName = 'Test Hospital';
        $import = new Import();

        $hospital = $this->createMock(HospitalInterface::class);
        $hospital->expects($this->exactly(1))
            ->method('getName')
            ->willReturn($hospitalName);

        $import->setHospital($hospital);
        $this->assertEquals($hospitalName, $import->getHospital()->getName());
    }

    public function testFilePath(): void
    {
        $path = '/dev/null';
        $import = new Import();

        $import->setFilePath($path);
        $this->assertEquals($path, $import->getFilePath());
    }

    public function testFileMimeType(): void
    {
        $valid = Import::MIME_CSV;
        $invalid = 'invalidMime';

        $import = new Import();

        $import->setFileMimeType($valid);
        $this->assertEquals($valid, $import->getFileMimeType());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('MIME-Type invalidMime is not supported');
        $import->setFileMimeType($invalid);
    }

    public function testExtension(): void
    {
        $valid = Import::EXT_CSV;
        $invalid = 'invalidExtension';

        $import = new Import();

        $import->setFileExtension($valid);
        $this->assertEquals($valid, $import->getFileExtension());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File extension invalidExtension is not supported');
        $import->setFileExtension($invalid);
    }

    public function testFileSize(): void
    {
        $valid = 1;
        $invalid = -1;

        $import = new Import();

        $import->setFileSize($valid);
        $this->assertEquals($valid, $import->getFileSize());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File size must be positive integer, not -1');
        $import->setFileSize($invalid);
    }

    public function testRowCount(): void
    {
        $valid = 1;
        $invalid = -1;

        $import = new Import();

        $import->setRowCount($valid);
        $this->assertEquals($valid, $import->getRowCount());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Row count must be positive integer, not -1');
        $import->setRowCount($invalid);
    }

    public function testRunCount(): void
    {
        $import = new Import();

        $this->assertEquals(0, $import->getRunCount());

        $import->bumpRunCount();
        $this->assertEquals(1, $import->getRunCount());
    }

    public function testRuntime(): void
    {
        $valid = 1;
        $invalid = -1;

        $import = new Import();

        $import->setRuntime($valid);
        $this->assertEquals($valid, $import->getRuntime());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Runtime must be positive integer, not -1');
        $import->setRuntime($invalid);
    }

    public function testId(): void
    {
        $id = 123;
        $import = new Import();

        $import->setId($id);
        $this->assertEquals($id, $import->getId());
    }

    public function testTimestamps(): void
    {
        $time = new \DateTime('NOW');
        $import = new Import();

        $this->assertInstanceOf(\DateTimeInterface::class, $import->getCreatedAt());
        $this->assertNull($import->getUpdatedAt());

        $import->setCreatedAt($time);
        $this->assertEquals($time, $import->getCreatedAt());

        $import->setUpdatedAt($time);
        $this->assertEquals($time, $import->getUpdatedAt());
    }

    public function testSkippedRows(): void
    {
        $import = new Import();

        $this->assertEquals(0, $import->getSkippedRows());

        $import->addSkippedRow();
        $this->assertEquals(1, $import->getSkippedRows());
    }
}
