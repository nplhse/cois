<?php

namespace App\Service\Mailers;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

interface MailerServiceInterface
{
    public function getEmail(string $recipient, string $subject, string $template, array $context = []): TemplatedEmail;

    public function getRoute(string $route, array $parameters = []): string;

    public function getTranslation(string $string, array $parameters = [], string $domain = 'emails'): string;

    public function send(TemplatedEmail $email): void;
}
