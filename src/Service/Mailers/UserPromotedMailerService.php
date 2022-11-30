<?php

namespace App\Service\Mailers;

use App\Entity\User;

class UserPromotedMailerService extends AbstractMailerService
{
    public function sendUserPromotedEmail(User $user): void
    {
        $email = $this->getEmail(
            $user->getEmail(),
            'account.promoted.title',
            'emails/user/promoted.html.twig',
            [
                'user' => $user,
                'targetUrl' => $this->getRoute('app_dashboard'),
            ],
        );

        $this->send($email);
    }
}
