<?php

namespace App\Service\Mailers;

use App\Entity\User;

class ImportReminderMailerService extends AbstractMailerService
{
    public function sendImportReminderEmail(User $user): void
    {
        $email = $this->getEmail(
            $user->getEmail(),
            'import.reminder.title',
            'emails/import/reminder.html.twig',
            [
                'username' => $user->getUsername(),
                'targetUrl' => $this->getRoute('app_import_new'),
            ],
        );

        $this->send($email);
    }
}
