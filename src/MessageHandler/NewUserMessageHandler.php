<?php

namespace App\MessageHandler;

use App\Message\NewUserMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

final class NewUserMessageHandler implements MessageHandlerInterface
{
    private MailerInterface $mailer;

    private RouterInterface $router;

    public function __construct(MailerInterface $mailer, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function __invoke(NewUserMessage $message): void
    {
        $email = (new TemplatedEmail())
            ->to(new Address($message->getUser()->getEmail()))
            ->subject('Welcome to Collaborative IVENA statistics')
            ->htmlTemplate('user/emails/welcome.html.twig')
            ->context([
                'user' => $message->getUser(),
                'hospital' => $message->getHospital(),
                'link' => $this->router->generate('app_login', [], UrlGenerator::ABSOLUTE_URL),
            ]);

        $this->mailer->send($email);
    }
}
