<?php

namespace App\Security;

use App\Entity\Import;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ImportVoter extends Voter
{
    private const DELETE = 'delete';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DELETE])) {
            return false;
        }

        // only vote on `Import` objects
        if (!$subject instanceof Import) {
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

        // you know $subject is a Import object, thanks to `supports()`
        /** @var Import $import */
        $import = $subject;

        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($import, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canDelete(Import $import, User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($import->getHospital()) {
            $hospitalOwner = $import->getHospital()->getOwner();
        } else {
            $hospitalOwner = null;
        }

        if ($hospitalOwner) {
            return $user === $hospitalOwner;
        } else {
            return $user === $import->getUser();
        }
    }
}
