<?php

namespace App\Application\Handler\Page;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Page\EditPageCommand;
use App\Domain\Event\Page\PageEditedEvent;
use App\Repository\PageRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class EditPageHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    private PageRepository $pageRepository;

    private SluggerInterface $slugger;

    public function __construct(PageRepository $pageRepository, SluggerInterface $slugger)
    {
        $this->pageRepository = $pageRepository;
        $this->slugger = $slugger;
    }

    public function __invoke(EditPageCommand $command): void
    {
        $page = $this->pageRepository->findOneBy(['id' => $command->getId()]);

        $page->setUpdatedBy($command->getUser());
        $page->setUpdatedAt(new \DateTime('NOW'));

        $page->setTitle($command->getTitle());
        $page->setSlug((string) $this->slugger->slug(strtolower($command->getTitle())));
        $page->setType($command->getType());
        $page->setStatus($command->getStatus());
        $page->setContent($command->getContent());

        $this->pageRepository->save();

        $this->dispatchEvent(new PageEditedEvent($page));
    }
}
