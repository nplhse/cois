<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Repository\PageRepository;
use Domain\Enum\PageStatus;
use Domain\Enum\PageType;
use Domain\Enum\PageVisbility;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('page_menu')]
final class PageMenuComponent
{
    public function __construct(
        private readonly Security $security,
        private readonly PageRepository $pageRepository,
    ) {
    }

    public function getPages(): array
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $this->pageRepository->findBy([
                'type' => PageType::GENERIC,
                ], [
                    'title' => 'ASC',
            ]);
        }

        if ($this->security->isGranted('ROLE_USER')) {
            return $this->pageRepository->findBy([
                'type' => PageType::GENERIC,
                'status' => PageStatus::PUBLISHED,
            ], [
                'title' => 'ASC',
            ]);
        }

        return $this->pageRepository->findBy([
            'type' => PageType::GENERIC,
            'status' => PageStatus::PUBLISHED,
            'visibility' => PageVisbility::PUBLIC,
        ], [
            'title' => 'ASC',
        ]);
    }
}
