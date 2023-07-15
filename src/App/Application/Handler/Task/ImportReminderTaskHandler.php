<?php

declare(strict_types=1);

namespace App\Application\Handler\Task;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Service\Mailers\ImportReminderMailerService;
use Domain\Command\Task\ImportReminderCommand;

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
