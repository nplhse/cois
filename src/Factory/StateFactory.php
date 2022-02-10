<?php

namespace App\Factory;

use App\Entity\State;
use App\Repository\StateRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<State>
 *
 * @method static State|Proxy createOne(array $attributes = [])
 * @method static State[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static State|Proxy find(object|array|mixed $criteria)
 * @method static State|Proxy findOrCreate(array $attributes)
 * @method static State|Proxy first(string $sortedField = 'id')
 * @method static State|Proxy last(string $sortedField = 'id')
 * @method static State|Proxy random(array $attributes = [])
 * @method static State|Proxy randomOrCreate(array $attributes = [])
 * @method static State[]|Proxy[] all()
 * @method static State[]|Proxy[] findBy(array $attributes)
 * @method static State[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static State[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static StateRepository|RepositoryProxy repository()
 * @method State|Proxy create(array|callable $attributes = [])
 */
final class StateFactory extends ModelFactory
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
            // ->afterInstantiate(function(State $state): void {})
        ;
    }

    protected static function getClass(): string
    {
        return State::class;
    }
}
