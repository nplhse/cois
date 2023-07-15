<?php

declare(strict_types=1);

namespace App\Service\Mailers;

use App\Entity\User;

class UserVerificationMailerService extends AbstractMailerService
{
    public function sendVerificationEmail(User $user, string $signedUrl, int $expiration): void
    {
        $email = $this->getEmail(
            $user->getEmail(),
            'account.verify.title',
            'emails/user/verify_email.html.twig',
            [
                'user' => $user,
                'signedUrl' => $signedUrl,
                'expiration' => '3600',
            ],
        );
    }
}
