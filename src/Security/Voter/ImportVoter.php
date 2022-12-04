<?php

namespace App\Security\Voter;

use App\Entity\Import;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ImportVoter extends Voter
{
    private const DELETE = 'delete';

    private const EDIT = 'edit';

    private const FILTER = 'filter';

    public function __construct(
        private Security $security
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::EDIT, self::DELETE, self::FILTER])) {
            return false;
        }

        // only vote on `Import` objects
        if (!$subject instanceof Import) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Import object, thanks to `supports()`
        /** @var Import $import */
        $import = $subject;

        return match ($attribute) {
            self::DELETE => $this->canDelete($import, $user),
            self::EDIT => $this->canEdit($import, $user),
            self::FILTER => $this->canFilter($import, $user),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canDelete(Import $import, User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $import->getHospital()->getOwner() === $import->getUser();
    }

    private function canEdit(Import $import, User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $import->getHospital()->getOwner() === $import->getUser();
    }

    private function canFilter(Import $import, User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $import->getHospital()->getOwner() === $import->getUser();
    }
}
