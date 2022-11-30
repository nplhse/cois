<?php

namespace App\Service\Mailers;

use App\Entity\User;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

class UserPasswordResetMailerService extends AbstractMailerService
{
    public function sendPasswordResetEmail(User $user, ResetPasswordToken $resetToken): void
    {
        $email = $this->getEmail(
            $user->getEmail(),
            'account.reset.title',
            'emails/user/reset_password.html.twig',
            [
                'resetToken' => $resetToken,
                'expiration' => '3600',
            ],
        );

        $this->send($email);
    }
}
