<?php

namespace App\Domain\Command\Task;

class ImportReminderCommand
{
    public function __construct(
        private array $recipients
    ) {
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }
}
