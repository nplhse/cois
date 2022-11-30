<?php

namespace App\Application\Handler\Task;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Task\ImportReminderCommand;
use App\Service\Mailers\ImportReminderMailerService;

class ImportReminderTaskHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private ImportReminderMailerService $mailer
    ) {
    }

    public function __invoke(ImportReminderCommand $command): void
    {
        $users = $command->getRecipients();

        foreach ($users as $user) {
            $this->mailer->sendImportReminderEmail($user);
        }
    }
}
