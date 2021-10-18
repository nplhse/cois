<?php

namespace App\Security;

use App\Entity\Hospital;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class HospitalVoter extends Voter
{
    private const EDIT = 'edit';

    private const VIEWSTATS = 'viewStats';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::EDIT, self::VIEWSTATS])) {
            return false;
        }

        // only vote on `Hospital` objects
        if (!$subject instanceof Hospital) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Hospital object, thanks to `supports()`
        /** @var Hospital $hospital */
        $hospital = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($hospital, $user);

            case self::VIEWSTATS:
                return $this->canViewStats($hospital, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Hospital $hospital, User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $user === $hospital->getOwner();
    }

    private function canViewStats(Hospital $hospital, User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($user === $hospital->getOwner()) {
            return true;
        }

        return false;
    }
}
