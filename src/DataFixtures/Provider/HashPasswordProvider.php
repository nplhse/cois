<?php

namespace App\DataFixtures\Provider;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HashPasswordProvider
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * HashPasswordProvider constructor.
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function hashPassword(string $plainPassword): string
    {
        return $this->passwordEncoder->encodePassword(new User(), $plainPassword);
    }
}
