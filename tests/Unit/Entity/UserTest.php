<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Hospital;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testProperties(): void
    {
        $username = 'foo';
        $email = 'foo@bar.com';
        $roles[] = 'ROLE_USER';
        $isVerified = true;
        $isCredentialsExpired = false;

        $user = new User();

        // Identifier/ Username
        $user->setUsername($username);
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($username, $user->getUserIdentifier());
        $this->assertEquals($username, (string) $user);

        // E-Mail
        $user->setEmail($email);
        $this->assertEquals($email, $user->getEmail());

        // Roles
        $this->assertEquals($roles, $user->getRoles());

        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $user->setRoles($roles);
        $this->assertEquals($roles, $user->getRoles());

        // isVerified
        $user->setIsVerified($isVerified);
        $this->assertEquals($isVerified, $user->getIsVerified());

        // isCredentialsExpired
        $user->setIsCredentialsExpired($isCredentialsExpired);
        $this->assertEquals($isCredentialsExpired, $user->getIsCredentialsExpired());
    }

    public function testSecurity(): void
    {
        $password = 's3cret';

        $user = new User();

        // Password
        $user->setPassword($password);
        $this->assertEquals($password, $user->getPassword());

        $user->setPlainPassword($password);
        $this->assertEquals($password, $user->getPlainPassword());
        $this->assertEmpty($user->getPassword());

        // Salt is not used
        $this->assertNull($user->getSalt());

        // Erase Credentials
        $user->eraseCredentials();
        $this->assertNull($user->getPlainPassword());
    }

    public function testHospitalRelationship(): void
    {
        $user = new User();
        $hospital = new Hospital();
        $hospital->setOwner($user);

        $user->setHospital($hospital);
        $this->assertEquals($hospital, $user->getHospital());
        $this->assertEquals($hospital->getOwner(), $user);
    }
}
