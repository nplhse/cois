<?php

namespace App\Factory;

use App\Entity\Import;
use App\Repository\ImportRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Import>
 *
 * @method static       Import|Proxy createOne(array $attributes = [])
 * @method static       Import[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static       Import|Proxy find(object|array|mixed $criteria)
 * @method static       Import|Proxy findOrCreate(array $attributes)
 * @method static       Import|Proxy first(string $sortedField = 'id')
 * @method static       Import|Proxy last(string $sortedField = 'id')
 * @method static       Import|Proxy random(array $attributes = [])
 * @method static       Import|Proxy randomOrCreate(array $attributes = [])
 * @method static       Import[]|Proxy[] all()
 * @method static       Import[]|Proxy[] findBy(array $attributes)
 * @method static       Import[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static       Import[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static       ImportRepository|RepositoryProxy repository()
 * @method Import|Proxy create(array|callable $attributes = [])
 */
final class ImportFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->sentence(3),
            'type' => Import::TYPE_ALLOCATION,
            'status' => Import::STATUS_SUCCESS,
            'createdAt' => self::faker()->dateTimeThisDecade(),
            'filePath' => 'dummy/path',
            'fileSize' => self::faker()->randomNumber(),
            'fileMimeType' => Import::MIME_PLAIN,
            'fileExtension' => Import::EXT_CSV,
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Import $import): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Import::class;
    }
}
