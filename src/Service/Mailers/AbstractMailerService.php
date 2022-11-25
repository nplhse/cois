<?php

namespace App\Service\Mailers;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractMailerService implements MailerServiceInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $router,
        private readonly MailerInterface $mailer,
        private readonly string $appMailerSender,
        private readonly string $appMailerFrom
    ) {
    }

    public function getEmail(string $recipient, string $subject, string $template, array $context = []): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->to(new Address($recipient))
            ->from(new Address($this->appMailerSender, $this->appMailerFrom))
            ->replyTo($this->appMailerSender)
            ->subject($this->getTranslation($subject))
            ->htmlTemplate($template)
            ->context($context);
    }

    public function getRoute(string $route, array $parameters = []): string
    {
        return $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getTranslation(string $string, array $parameters = [], string $domain = 'emails'): string
    {
        return $this->translator->trans($string, $parameters, $domain);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(TemplatedEmail $email): void
    {
        $this->mailer->send($email);
    }
}
