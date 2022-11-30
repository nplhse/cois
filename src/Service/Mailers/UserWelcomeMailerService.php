<?php

namespace App\Service\Mailers;

use App\Entity\User;

class UserWelcomeMailerService extends AbstractMailerService
{
    public function sendWelcomeEmail(User $user): void
    {
        $email = $this->getEmail(
            $user->getEmail(),
            'account.welcome.title',
            'emails/user/welcome.html.twig',
            [
                'username' => $user->getUsername(),
                'targetUrl' => $this->getRoute('app_login'),
            ],
        );

        $this->send($email);
    }
}
