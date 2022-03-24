<?php

declare(strict_types=1);

namespace App\Domain\Event\Page;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Event\NamedEventTrait;
use App\Entity\Page;
use Symfony\Contracts\EventDispatcher\Event;

class PageEditedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'page.edited';

    private Page $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function getPage(): Page
    {
        return $this->page;
    }
}
