<?php

declare(strict_types=1);

namespace Domain\Event\Page;

use App\Entity\Page;
use Domain\Contracts\DomainEventInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class PageDeletedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'page.deleted';

    public function __construct(
        private Page $page
    ) {
    }

    public function getPage(): Page
    {
        return $this->page;
    }
}
