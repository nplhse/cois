<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Enum\PageStatus;
use App\Domain\Enum\PageType;
use App\Domain\Enum\PageVisbility;
use App\Factory\AllocationFactory;
use App\Factory\CategoryFactory;
use App\Factory\CommentFactory;
use App\Factory\DispatchAreaFactory;
use App\Factory\HospitalFactory;
use App\Factory\ImportFactory;
use App\Factory\PageFactory;
use App\Factory\PostFactory;
use App\Factory\StateFactory;
use App\Factory\SupplyAreaFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        PageFactory::createOne([
            'title' => 'Imprint',
            'slug' => 'imprint',
            'type' => PageType::IMPRINT,
            'status' => PageStatus::PUBLISHED,
            'visibility' => PageVisbility::PUBLIC,
            'createdBy' => UserFactory::random(),
        ]);
        PageFactory::createOne([
            'title' => 'Privacy',
            'slug' => 'privacy',
            'type' => PageType::PRIVACY,
            'status' => PageStatus::PUBLISHED,
            'visibility' => PageVisbility::PUBLIC,
            'createdBy' => UserFactory::random(),
        ]);
        PageFactory::createOne([
            'title' => 'Terms and conditions',
            'slug' => 'terms',
            'type' => PageType::TERMS,
            'status' => PageStatus::PUBLISHED,
            'visibility' => PageVisbility::PUBLIC,
            'createdBy' => UserFactory::random(),
        ]);

        StateFactory::createMany(3);

        DispatchAreaFactory::createMany(8, static fn () => ['state' => StateFactory::random()]);

        SupplyAreaFactory::createMany(3, static fn () => ['state' => StateFactory::random()]);

        HospitalFactory::createMany(random_int(3, 10), static fn () => [
            'owner' => UserFactory::random(),
            'state' => StateFactory::random(),
            'dispatchArea' => DispatchAreaFactory::random(),
            'supplyArea' => SupplyAreaFactory::random(),
        ]);

        ImportFactory::createMany(random_int(3, 5), static fn () => [
            'user' => UserFactory::random(),
            'hospital' => HospitalFactory::random(),
        ]);

        AllocationFactory::createMany(random_int(128, 256), static fn () => [
            'hospital' => HospitalFactory::random(),
            'import' => ImportFactory::random(),
            'state' => StateFactory::random(),
            'dispatchArea' => DispatchAreaFactory::random(),
            'supplyArea' => SupplyAreaFactory::random(),
        ]);

        /*
         * Add Blog to website
         */
        CategoryFactory::new()->create(['name' => 'Demo Category']);
        CategoryFactory::new()->create(['name' => 'Sample Category']);
        CategoryFactory::new()->create(['name' => 'Another Category']);

        TagFactory::new()->create(['name' => 'Demo Tag']);
        TagFactory::new()->create(['name' => 'Sample Tag']);
        TagFactory::new()->create(['name' => 'Another Tag']);
        TagFactory::new()->create(['name' => 'Yet Another Tag']);

        PostFactory::new()
            ->many(1)
            ->create(function () {
                return [
                    'isSticky' => true,
                    'tags' => TagFactory::randomSet(2),
                    'category' => CategoryFactory::random(),
                    'comments' => CommentFactory::createMany(5),
                    'createdBy' => UserFactory::random(),
                ];
            });

        PostFactory::new()
            ->many(14)
            ->create(function () {
                return [
                    'tags' => TagFactory::randomSet(2),
                    'category' => CategoryFactory::random(),
                    'createdBy' => UserFactory::random(),
                ];
            });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
