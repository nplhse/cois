<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Post;
use App\Repository\PostRepository;
use Domain\Enum\PostStatus;
use Symfony\Component\String\Slugger\SluggerInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Post>
 *
 * @method        Post|Proxy                     create(array|callable $attributes = [])
 * @method static Post|Proxy                     createOne(array $attributes = [])
 * @method static Post|Proxy                     find(object|array|mixed $criteria)
 * @method static Post|Proxy                     findOrCreate(array $attributes)
 * @method static Post|Proxy                     first(string $sortedField = 'id')
 * @method static Post|Proxy                     last(string $sortedField = 'id')
 * @method static Post|Proxy                     random(array $attributes = [])
 * @method static Post|Proxy                     randomOrCreate(array $attributes = [])
 * @method static PostRepository|RepositoryProxy repository()
 * @method static Post[]|Proxy[]                 all()
 * @method static Post[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Post[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Post[]|Proxy[]                 findBy(array $attributes)
 * @method static Post[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Post[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class PostFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'allowComments' => self::faker()->boolean(),
            'category' => CategoryFactory::random(),
            'tags' => TagFactory::randomSet(2),
            'content' => self::faker()->text(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeThisDecade()),
            'createdBy' => UserFactory::random(),
            'isSticky' => false,
            'status' => self::faker()->randomElement([PostStatus::Draft, PostStatus::Published]),
            'title' => self::faker()->sentence(random_int(3, 6)),
            'teaser' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function (Post $post): void {
                $post->setSlug($this->slugger->slug($post->getTitle())->lower()->toString());
            })
        ;
    }

    protected static function getClass(): string
    {
        return Post::class;
    }
}
