<?php

namespace App\Tests\Unit\Domain\Entity;

use _PHPStan_c862bb974\Nette\Utils\DateTime;
use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUsername(): void
    {
        $username = 'foo';
        $user = new User();

        $user->setUsername($username);
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($username, $user->getUserIdentifier());
        $this->assertEquals($username, (string) $user);
    }

    public function testEmail(): void
    {
        $email = 'foo@bar.com';
        $user = new User();

        $user->setEmail($email);
        $this->assertEquals($email, $user->getEmail());
    }

    public function testDefaultUserRole(): void
    {
        $user = new User();

        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testRoles(): void
    {
        $user = new User();

        $user->addRole('ROLE_ADMIN');
        $this->assertEquals(['ROLE_USER', 'ROLE_ADMIN'], $user->getRoles());

        $user->removeRole('ROLE_ADMIN');
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User has no Role ROLE_TEST');
        $user->removeRole('ROLE_TEST');
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

    public function testVerification(): void
    {
        $user = new User();

        $this->assertEquals(false, $user->isVerified());

        $user->verify();
        $this->assertEquals(true, $user->isVerified());

        $user->unverify();
        $this->assertEquals(false, $user->isVerified());
    }

    public function testParticipation(): void
    {
        $user = new User();

        $this->assertEquals(false, $user->isParticipant());

        $user->enableParticipation();
        $this->assertEquals(true, $user->isParticipant());

        $user->disableParticipation();
        $this->assertEquals(false, $user->isParticipant());
    }
}
