<?php

namespace App\Factory;

use App\Domain\Enum\Setting\SettingTypeEnum;
use App\Entity\Setting;
use App\Repository\SettingRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Setting>
 *
 * @method static        Setting|Proxy createOne(array $attributes = [])
 * @method static        Setting[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static        Setting|Proxy find(object|array|mixed $criteria)
 * @method static        Setting|Proxy findOrCreate(array $attributes)
 * @method static        Setting|Proxy first(string $sortedField = 'id')
 * @method static        Setting|Proxy last(string $sortedField = 'id')
 * @method static        Setting|Proxy random(array $attributes = [])
 * @method static        Setting|Proxy randomOrCreate(array $attributes = [])
 * @method static        Setting[]|Proxy[] all()
 * @method static        Setting[]|Proxy[] findBy(array $attributes)
 * @method static        Setting[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static        Setting[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static        SettingRepository|RepositoryProxy repository()
 * @method Setting|Proxy create(array|callable $attributes = [])
 */
final class SettingFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->word(),
            'value' => self::faker()->words(3),
            'type' => self::faker()->randomElement(SettingTypeEnum::getChoices()),
            'category' => self::faker()->word(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Setting $setting): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Setting::class;
    }
}
