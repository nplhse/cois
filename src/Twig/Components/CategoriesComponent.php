<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Repository\CategoryRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('categories')]
final class CategoriesComponent
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function getCategories(): array
    {
        // an example method that returns an array of Products
        return $this->categoryRepository->findAll();
    }
}
