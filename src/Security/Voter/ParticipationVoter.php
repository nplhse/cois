<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ParticipationVoter extends Voter
{
    private const CREATE_HOSPITAL = 'create_hospital';

    private const CREATE_IMPORT = 'create_import';

    private const FILTER_STATISTICS = 'filter_statistics';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::CREATE_HOSPITAL, self::CREATE_IMPORT, self::FILTER_STATISTICS])) {
            return false;
        }

        // only vote on `User` objects
        if (!$subject instanceof User) {
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

        return match ($attribute) {
            self::CREATE_HOSPITAL => $this->canCreateHospital($subject),
            self::CREATE_IMPORT => $this->canCreateImport($subject),
            self::FILTER_STATISTICS => $this->canFilterStatistics($subject),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canCreateHospital(User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $user->isParticipant();
    }

    private function canCreateImport(User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$user->isParticipant()) {
            return false;
        }

        if ($user->getHospitals()->isEmpty()) {
            return false;
        }

        return true;
    }

    private function canFilterStatistics(User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$user->isParticipant()) {
            return false;
        }

        if ($user->getHospitals()->isEmpty()) {
            return false;
        }

        return true;
    }
}
