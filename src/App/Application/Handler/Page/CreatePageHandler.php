<?php

declare(strict_types=1);

namespace App\Application\Handler\Page;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Entity\Page;
use App\Repository\PageRepository;
use Domain\Command\Page\CreatePageCommand;
use Domain\Enum\PageStatus;
use Domain\Enum\PageType;
use Domain\Event\Page\PageCreatedEvent;
use Symfony\Component\String\Slugger\SluggerInterface;

class CreatePageHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private PageRepository $pageRepository,
        private SluggerInterface $slugger
    ) {
    }

    public function __invoke(CreatePageCommand $command): void
    {
        $page = new Page();

        $page->setCreatedBy($command->getUser());
        $page->setCreatedAt(new \DateTime('NOW'));

        $page->setTitle($command->getTitle());
        $page->setSlug((string) $this->slugger->slug(strtolower($command->getTitle())));
        $page->setContent($command->getContent());

        $type = match ($command->getType()) {
            'imprint' => PageType::IMPRINT,
            'privacy' => PageType::PRIVACY,
            'terms' => PageType::TERMS,
            'about' => PageType::ABOUT,
            default => PageType::GENERIC,
        };

        $page->setType($type);

        $status = match ($command->getStatus()) {
            'draft' => PageStatus::DRAFT,
            'published' => PageStatus::PUBLISHED,
        };

        $page->setStatus($status);

        $this->pageRepository->add($page);

        $this->dispatchEvent(new PageCreatedEvent($page));
    }
}
