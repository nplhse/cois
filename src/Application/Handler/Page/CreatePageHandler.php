<?php

namespace App\Application\Handler\Page;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Page\CreatePageCommand;
use App\Domain\Event\Page\PageCreatedEvent;
use App\Entity\Page;
use App\Repository\PageRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class CreatePageHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    private PageRepository $pageRepository;

    private SluggerInterface $slugger;

    public function __construct(PageRepository $pageRepository, SluggerInterface $slugger)
    {
        $this->pageRepository = $pageRepository;
        $this->slugger = $slugger;
    }

    public function __invoke(CreatePageCommand $command): void
    {
        $page = new Page();

        $page->setCreatedBy($command->getUser());
        $page->setCreatedAt(new \DateTime('NOW'));

        $page->setTitle($command->getTitle());
        $page->setSlug((string) $this->slugger->slug(strtolower($command->getTitle())));
        $page->setType($command->getType());
        $page->setStatus($command->getStatus());
        $page->setContent($command->getContent());

        $this->pageRepository->add($page);

        $this->dispatchEvent(new PageCreatedEvent($page));
    }
}
