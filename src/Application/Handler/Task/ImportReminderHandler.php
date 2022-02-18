<?php

namespace App\Application\Handler\Task;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Task\ImportReminderCommand;
use App\Service\MailerService;

class ImportReminderHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    private MailerService $mailer;

    public function __construct(MailerService $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(ImportReminderCommand $command): void
    {
        $users = $command->getRecipients();

        foreach ($users as $user) {
            $this->mailer->sendImportReminderEmail($user);
        }
    }
}
