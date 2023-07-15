<?php

declare(strict_types=1);

namespace App\Service\Mailers;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

abstract class AbstractNotificationService extends AbstractMailerService
{
    public function getEmail(string $recipient, string $subject, string $template, array $context = []): TemplatedEmail
    {
        return (new NotificationEmail())
            ->to(new Address($recipient))
            ->from(new Address($this->appMailerAddress, $this->appMailerSender))
            ->replyTo($this->appMailerAddress)
            ->subject($this->getTranslation($subject))
            ->htmlTemplate($template)
            ->context($context);
    }
}
