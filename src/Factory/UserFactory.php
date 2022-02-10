<?php

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<User>
 *
 * @method static     User|Proxy createOne(array $attributes = [])
 * @method static     User[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static     User|Proxy find(object|array|mixed $criteria)
 * @method static     User|Proxy findOrCreate(array $attributes)
 * @method static     User|Proxy first(string $sortedField = 'id')
 * @method static     User|Proxy last(string $sortedField = 'id')
 * @method static     User|Proxy random(array $attributes = [])
 * @method static     User|Proxy randomOrCreate(array $attributes = [])
 * @method static     User[]|Proxy[] all()
 * @method static     User[]|Proxy[] findBy(array $attributes)
 * @method static     User[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static     User[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static     UserRepository|RepositoryProxy repository()
 * @method User|Proxy create(array|callable $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();

        $this->passwordHasher = $passwordHasher;
    }

    protected function getDefaults(): array
    {
        return [
            'username' => self::faker()->userName(),
            'email' => self::faker()->safeEmail(),
            'roles' => ['ROLE_USER'],
            'plainPassword' => 'password',
            'isVerified' => self::faker()->boolean(),
            'isParticipant' => self::faker()->boolean(),
            'createdAt' => self::faker()->dateTimeThisDecade(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->withFullAccess()
            ->afterInstantiate(function (User $user) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
            })
        ;
    }

    public function withFullAccess(): self
    {
        return $this->addState([
            'isVerified' => true,
            'isParticipant' => true,
        ]);
    }

    public function withNoAccess(): self
    {
        return $this->addState([
            'isVerified' => false,
            'isParticipant' => false,
        ]);
    }

    public function asAdmin(): self
    {
        return $this->addState([
            'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
        ]);
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
