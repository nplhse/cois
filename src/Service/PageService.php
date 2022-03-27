<?php

namespace App\Service;

use App\Domain\Enum\Page\PageTypeEnum;
use App\Repository\PageRepository;

class PageService
{
    private PageRepository $pageRepository;

    private array $pageStore;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function hasPage(string $slug): bool
    {
        if (isset($this->pageStore[$slug])) {
            return true;
        }

        $page = match ($slug) {
            'imprint' => $this->pageRepository->findOneBy(['type' => PageTypeEnum::ImprintPage]),
            'privacy' => $this->pageRepository->findOneBy(['type' => PageTypeEnum::PrivacyPage]),
            'terms' => $this->pageRepository->findOneBy(['type' => PageTypeEnum::TermsPage]),
            default => $this->pageRepository->findOneBy(['slug' => $slug]),
        };

        if (null === $page) {
            return false;
        }

        $this->pageStore[$slug] = $page;

        return true;
    }

    public function getSlug(string $target): ?string
    {
        if ($this->hasPage($target)) {
            return $this->pageStore[$target]->getSlug();
        }

        return null;
    }
}
