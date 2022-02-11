<?php

namespace App\Factory;

use App\Entity\DispatchArea;
use App\Repository\DispatchAreaRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<DispatchArea>
 *
 * @method static             DispatchArea|Proxy createOne(array $attributes = [])
 * @method static             DispatchArea[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static             DispatchArea|Proxy find(object|array|mixed $criteria)
 * @method static             DispatchArea|Proxy findOrCreate(array $attributes)
 * @method static             DispatchArea|Proxy first(string $sortedField = 'id')
 * @method static             DispatchArea|Proxy last(string $sortedField = 'id')
 * @method static             DispatchArea|Proxy random(array $attributes = [])
 * @method static             DispatchArea|Proxy randomOrCreate(array $attributes = [])
 * @method static             DispatchArea[]|Proxy[] all()
 * @method static             DispatchArea[]|Proxy[] findBy(array $attributes)
 * @method static             DispatchArea[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static             DispatchArea[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static             DispatchAreaRepository|RepositoryProxy repository()
 * @method DispatchArea|Proxy create(array|callable $attributes = [])
 */
final class DispatchAreaFactory extends ModelFactory
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
            // ->afterInstantiate(function(DispatchArea $dispatchArea): void {})
        ;
    }

    protected static function getClass(): string
    {
        return DispatchArea::class;
    }
}
