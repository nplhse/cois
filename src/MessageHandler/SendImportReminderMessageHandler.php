<?php

namespace App\MessageHandler;

use App\Message\SendImportReminderMessage;
use App\Service\MailerService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendImportReminderMessageHandler implements MessageHandlerInterface
{
    private MailerService $mailer;

    public function __construct(MailerService $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(SendImportReminderMessage $message): void
    {
        $users = $message->getRecipients();

        foreach ($users as $user) {
            if ($user->getAllowsEmail() && $user->getAllowsEmailReminder()) {
                $this->mailer->sendImportReminderEmail($user);
            }
        }
    }
}
