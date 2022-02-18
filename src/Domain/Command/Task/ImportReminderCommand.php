<?php

namespace App\Domain\Command\Task;

class ImportReminderCommand
{
    private array $recipients;

    public function __construct(array $recipients)
    {
        $this->recipients = $recipients;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }
}
