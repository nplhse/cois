<?php

namespace App\Twig;

use App\Service\PageService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PageExtension extends AbstractExtension
{
    private PageService $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_page', [$this->pageService, 'hasPage']),
            new TwigFunction('get_slug', [$this->pageService, 'getSlug']),
            new TwigFunction('get_title', [$this->pageService, 'getTitle']),
        ];
    }
}
