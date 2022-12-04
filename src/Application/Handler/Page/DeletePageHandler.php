<?php

namespace App\Application\Handler\Page;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Page\DeletePageCommand;
use App\Domain\Event\Page\PageDeletedEvent;
use App\Repository\PageRepository;

class DeletePageHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private PageRepository $pageRepository
    ) {
    }

    public function __invoke(DeletePageCommand $command): void
    {
        $page = $this->pageRepository->findOneBy(['id' => $command->getId()]);

        $this->pageRepository->remove($page);

        $this->dispatchEvent(new PageDeletedEvent($page));
    }
}
