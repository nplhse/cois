<?php

namespace App\Service;

use App\Entity\Page;
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

        $page = $this->pageRepository->findOneBy(['slug' => $slug]);

        if (null === $page) {
            return false;
        }

        $this->pageStore[$slug] = $page;

        return true;
    }

    public function getPage(string $slug): ?Page
    {
        if (isset($this->pageStore[$slug])) {
            return $this->pageStore[$slug];
        }

        $page = $this->pageRepository->findOneBy(['slug' => $slug]);

        if (null === $page) {
            return null;
        }

        $this->pageStore[$slug] = $page;

        return $page;
    }
}
