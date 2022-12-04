<?php

declare(strict_types=1);

namespace App\Domain\Event\Page;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Event\NamedEventTrait;
use App\Entity\Page;
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
