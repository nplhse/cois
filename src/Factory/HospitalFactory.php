<?php

namespace App\Factory;

use App\Entity\Hospital;
use App\Repository\HospitalRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Hospital>
 *
 * @method static Hospital|Proxy                     createOne(array $attributes = [])
 * @method static Hospital[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Hospital|Proxy                     find(object|array|mixed $criteria)
 * @method static Hospital|Proxy                     findOrCreate(array $attributes)
 * @method static Hospital|Proxy                     first(string $sortedField = 'id')
 * @method static Hospital|Proxy                     last(string $sortedField = 'id')
 * @method static Hospital|Proxy                     random(array $attributes = [])
 * @method static Hospital|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Hospital[]|Proxy[]                 all()
 * @method static Hospital[]|Proxy[]                 findBy(array $attributes)
 * @method static Hospital[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Hospital[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static HospitalRepository|RepositoryProxy repository()
 * @method        Hospital|Proxy                     create(array|callable $attributes = [])
 */
final class HospitalFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->company(),
            'address' => self::faker()->address(),
            'createdAt' => self::faker()->dateTimeThisDecade(),
            'beds' => self::faker()->numberBetween(100, 1500),
            'location' => self::faker()->randomElement([Hospital::LOCATION_URBAN, Hospital::LOCATION_RURAL]),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Hospital $hospital): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Hospital::class;
    }
}
