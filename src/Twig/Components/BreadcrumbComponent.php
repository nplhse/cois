<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('breadcrumb')]
final class BreadcrumbComponent
{
    public array $items = [];

    public function mount(array $items): void
    {
        $this->items[] = ['link.home' => ''];

        $this->items = array_merge($this->items, $items);
    }
}
