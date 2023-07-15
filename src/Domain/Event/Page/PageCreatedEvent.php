<?php

declare(strict_types=1);

namespace Domain\Event\Page;

use App\Entity\Page;
use Domain\Contracts\DomainEventInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class PageCreatedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'page.created';

    public function __construct(
        private Page $page
    ) {
    }

    public function getPage(): Page
    {
        return $this->page;
    }
}
