<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Page;
use Domain\Contracts\UserInterface;
use Domain\Enum\PageStatus;
use Domain\Enum\PageVisbility;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PageVoter extends Voter
{
    private const DELETE = 'delete';

    private const EDIT = 'edit';

    private const VIEW = 'view';

    public function __construct(
        private Security $security
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::EDIT, self::DELETE, self::VIEW])) {
            return false;
        }

        // only vote on `Page` objects
        if (!$subject instanceof Page) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var ?UserInterface $user */
        $user = $token->getUser();

        // you know $subject is a Page object, thanks to `supports()`
        /** @var Page $page */
        $page = $subject;

        return match ($attribute) {
            self::DELETE => $this->canDelete($page, $user),
            self::EDIT => $this->canEdit($page, $user),
            self::VIEW => $this->canView($page, $user),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canDelete(Page $page, ?UserInterface $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

    private function canEdit(Page $page, ?UserInterface $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

    private function canView(Page $page, ?UserInterface $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN', $user)) {
            return true;
        }

        if (PageVisbility::PRIVATE === $page->getVisibility() && !$this->security->isGranted('ROLE_USER', $user)) {
            return false;
        }

        if (PageStatus::PUBLISHED === $page->getStatus()) {
            return true;
        }

        return false;
    }
}
