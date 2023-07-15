<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Page;
use App\Repository\PageRepository;
use Domain\Enum\PageStatus;
use Domain\Enum\PageType;
use Domain\Enum\PageVisbility;
use Symfony\Component\String\Slugger\SluggerInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Page>
 *
 * @method static Page|Proxy                     createOne(array $attributes = [])
 * @method static Page[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Page|Proxy                     find(object|array|mixed $criteria)
 * @method static Page|Proxy                     findOrCreate(array $attributes)
 * @method static Page|Proxy                     first(string $sortedField = 'id')
 * @method static Page|Proxy                     last(string $sortedField = 'id')
 * @method static Page|Proxy                     random(array $attributes = [])
 * @method static Page|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Page[]|Proxy[]                 all()
 * @method static Page[]|Proxy[]                 findBy(array $attributes)
 * @method static Page[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Page[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static PageRepository|RepositoryProxy repository()
 * @method        Page|Proxy                     create(array|callable $attributes = [])
 */
final class PageFactory extends ModelFactory
{
    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->sentence(4),
            'slug' => '',
            'content' => self::faker()->text(),
            'status' => self::faker()->randomElement(PageStatus::cases()),
            'type' => self::faker()->randomElement(PageType::cases()),
            'visibility' => self::faker()->randomElement(PageVisbility::cases()),
            'createdAt' => self::faker()->dateTimeThisDecade(),
            'createdBy' => UserFactory::random()->getId(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function (Page $page): void {
                $page->setSlug($this->slugger->slug($page->getTitle())->lower()->toString());
            })
        ;
    }

    protected static function getClass(): string
    {
        return Page::class;
    }
}
