<?php

namespace App\Message;

final class SendImportReminderMessage
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
