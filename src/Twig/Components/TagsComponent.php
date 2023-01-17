<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Repository\TagRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('tags')]
final class TagsComponent
{
    public function __construct(
        private readonly TagRepository $tagRepository,
    ) {
    }

    public function getTags(): array
    {
        // an example method that returns an array of Products
        return $this->tagRepository->findAllByName();
    }
}
