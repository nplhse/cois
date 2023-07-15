<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\PageRepository;
use Domain\Enum\PageType;

class PageService
{
    private array $pageStore;

    public function __construct(
        private PageRepository $pageRepository
    ) {
    }

    public function hasPage(string $target): bool
    {
        if (isset($this->pageStore[$target])) {
            return true;
        }

        $page = match ($target) {
            'imprint' => $this->pageRepository->findOneBy(['type' => PageType::IMPRINT]),
            'privacy' => $this->pageRepository->findOneBy(['type' => PageType::PRIVACY]),
            'terms' => $this->pageRepository->findOneBy(['type' => PageType::TERMS]),
            'about' => $this->pageRepository->findOneBy(['type' => PageType::ABOUT]),
            default => $this->pageRepository->findOneBy(['slug' => $target]),
        };

        if (null === $page) {
            return false;
        }

        $this->pageStore[$target] = $page;

        return true;
    }

    public function getSlug(string $target): ?string
    {
        if ($this->hasPage($target)) {
            return $this->pageStore[$target]->getSlug();
        }

        return null;
    }

    public function getTitle(string $target): ?string
    {
        if ($this->hasPage($target)) {
            return $this->pageStore[$target]->getTitle();
        }

        return null;
    }
}
