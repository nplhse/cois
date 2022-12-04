<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Domain\Contracts\UserInterface;
use App\Domain\Enum\Page\PageStatusEnum;
use App\Domain\Enum\Page\PageTypeEnum;
use App\Entity\Page;
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
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (PageStatusEnum::Published !== $page->getStatus()) {
            return false;
        }

        switch ($page->getType()) {
            case PageTypeEnum::Public:
            case PageTypeEnum::PrivacyPage:
            case PageTypeEnum::ImprintPage:
            case PageTypeEnum::TermsPage:
                return true;
            case PageTypeEnum::Private:
                if ($this->security->isGranted('ROLE_USER')) {
                    return true;
                }
                // no break
            default:
                break;
        }

        return false;
    }
}
