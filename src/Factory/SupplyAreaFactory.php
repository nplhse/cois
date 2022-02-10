<?php

namespace App\Factory;

use App\Entity\SupplyArea;
use App\Repository\SupplyAreaRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<SupplyArea>
 *
 * @method static           SupplyArea|Proxy createOne(array $attributes = [])
 * @method static           SupplyArea[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static           SupplyArea|Proxy find(object|array|mixed $criteria)
 * @method static           SupplyArea|Proxy findOrCreate(array $attributes)
 * @method static           SupplyArea|Proxy first(string $sortedField = 'id')
 * @method static           SupplyArea|Proxy last(string $sortedField = 'id')
 * @method static           SupplyArea|Proxy random(array $attributes = [])
 * @method static           SupplyArea|Proxy randomOrCreate(array $attributes = [])
 * @method static           SupplyArea[]|Proxy[] all()
 * @method static           SupplyArea[]|Proxy[] findBy(array $attributes)
 * @method static           SupplyArea[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static           SupplyArea[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static           SupplyAreaRepository|RepositoryProxy repository()
 * @method SupplyArea|Proxy create(array|callable $attributes = [])
 */
final class SupplyAreaFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => ucfirst(self::faker()->word()),
            'createdAt' => self::faker()->dateTimeThisDecade(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(SupplyArea $supplyArea): void {})
        ;
    }

    protected static function getClass(): string
    {
        return SupplyArea::class;
    }
}
