<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Repository\PostRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('recent_posts')]
final class RecentPostsComponent
{
    public int $limit = 5;

    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function getRecentPosts(): array
    {
        // an example method that returns an array of Products
        return $this->postRepository->findRecentPosts($this->limit);
    }
}
